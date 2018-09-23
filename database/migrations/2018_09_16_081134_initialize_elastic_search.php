<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Helpers\ElasticSearchClientHelper;
use Illuminate\Support\Facades\Log;

class InitializeElasticSearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $client = ElasticSearchClientHelper::getClient();
        if (is_a($client, '\Elasticsearch\Client')) {
            $params = [
                'index' => config('app.es.index'),
                'body' => [
                    'settings' => [
                        'analysis' => [
                            'analyzer' => [
                                'email_analyzer' => [
                                    'tokenizer' => 'email_tokenizer',
                                ],
                            ],
                            'tokenizer' => [
                                'email_tokenizer' => [
                                    'type' => 'uax_url_email',
                                    'max_token_length' => 255,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        '_default_' => [
                            'properties' => [
                                'email' => [
                                    'type' => 'text',
                                    'analyzer' => 'email_analyzer',
                                    'term_vector' => 'yes',
                                    'fielddata' => true,
                                ],
                                'created_at' => [
                                    'type' => 'date',
                                    'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis',
                                ],
                                'updated_at' => [
                                    'type' => 'date',
                                    'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
            try {
                $result = $client->indices()->create($params);
                Log::info('Created index with mapping successfully');
            } catch (\Exception $e) {
                $result = json_decode($result = $e->getMessage());
                Log::critical('Failed to create index.');
                Log::critical(print_r($result, true));
                return false;
                throw new \Exception('Failed to create index. See logs for more information.', 1);
                
            }
        } else {
            return false;
            throw new \Exception('ElasticSearch is not enabled or service is unreachable', 1);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $client = ElasticSearchClientHelper::getClient();
        if (is_a($client, '\Elasticsearch\Client')) {
            $params = [
                'index' => config('app.es.index'),
            ];
            try {
                $result = $client->indices()->delete($params);
                Log::info('Deleted index successfully');
            } catch (\Exception $e) {
                $result = json_decode($result = $e->getMessage());
                Log::critical('Failed to delete index.');
                Log::critical(print_r($result, true));
                return false;
                throw new \Exception('Failed to delete index. See logs for more information.', 1);
            }
        } else {
            return false;
            throw new \Exception('ElasticSearch is not enabled or service is unreachable', 1);
        }
    }
}
