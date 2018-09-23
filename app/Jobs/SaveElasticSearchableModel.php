<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveElasticSearchableModel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $action;
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $action = 'create')
    {
        $this->model = $model;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = \App\Helpers\ElasticSearchClientHelper::getClient();
        if (!is_a($client, '\Elasticsearch\Client') || !\App\Helpers\ElasticSearchClientHelper::clientCanConnect($client)) {
            return false;
        }
        $searchable_fields = $this->model->getSearchableColumns();
        if ('create' == $this->action) {
            $body = [
                'model' => $this->model->getMorphClass(),
                'model_id' => $this->model->id,
            ];
        } else {
            $body = [];
        }
        foreach ($searchable_fields as $field) {
            $body[$field] = $this->model->{$field};
        }
        $created_at = $this->model->created_at;
        if (!is_null($created_at) && strlen($created_at) > 0) {
            $body['created_at'] = $created_at->toDateTimeString();
        }
        $updated_at = $this->model->updated_at;
        if (!is_null($updated_at) && strlen($updated_at) > 0) {
            $body['updated_at'] = $updated_at->toDateTimeString();
        }
        $params = [
            'index' => config('app.es.index'),
            'type' => 'model',
            'body' => $body,
        ];
        if ('create' !== $this->action) {
            sleep(1);
            $search_params = [
                'index' => config('app.es.index'),
                'type' => 'model',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'match' => [
                                        'model.keyword' => $this->model->getMorphClass(),
                                    ],
                                ],
                                [
                                    'match' => [
                                        'model_id' => $this->model->id,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
            try {
                $search_results = $client->search($search_params);
            } catch (\Exception $e) {
                $search_results = json_decode($e->getMessage(), true);
            }
            $success = true;
            if (is_array($search_results)
                && array_key_exists('hits', $search_results)
                && is_array($search_results['hits'])
                && array_key_exists('total', $search_results['hits'])
                && intval($search_results['hits']['total'] >= 1)
            ) {
                foreach ($search_results['hits']['hits'] as $esmodel) {
                    $update_params = $params;
                    $update_params['id'] = $esmodel['_id'];
                    $update_params['body'] = [
                        'doc' => $params['body'],
                        'detect_noop' => false,
                    ];
                    try {
                        $update_results = $client->update($update_params);
                    } catch (\Exception $e) {
                        $update_results = json_decode($e->getMessage(), true);
                    }
                    if ($success === true) {
                        $success = (is_array($update_results) && array_key_exists('result', $update_results) && ('updated' == $update_results['result'] || 'noop' == $update_results['result']));
                    }
                }
            } else {
                $success = false;
            }
            return $success;
        }
        try {
            $response = $client->index($params);
        } catch (\Exception $e) {
            $response = json_decode($e->getMessage(), true);
        }
        return (is_array($response) && array_key_exists('result', $response) && 'created' == $response['result']);
    }
}
