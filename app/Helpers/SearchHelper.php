<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\ElasticSearchClientHelper;

class SearchHelper
{
    protected $models = [];
    protected $namespace = 'App\\';
    protected $client;

    public function __construct()
    {
        $path = app_path();
        try {
            $df = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($df as $item) {
                if ($item->isReadable() && $item->isFile() && mb_strtolower($item->getExtension()) === 'php') {
                    $class = str_replace("/", "\\", mb_substr($item->getRealPath(), mb_strlen($path), -4));
                    $class = sprintf('%s%s', ('\\' == substr($this->namespace, -1)) ? substr($this->namespace, 0, strlen($this->namespace) - 1) : $this->namespace, $class);
                    array_push($this->models, $class);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
        $this->models = array_filter($this->models, [$this, 'returnOnlyModels']);
        $this->models = array_filter($this->models, [$this, 'returnOnlySearchableModels']);
        $this->client = ElasticSearchClientHelper::getClient();
    }

    public function searchModelForResults($term, $model, &$return = [])
    {
        if (!in_array($model, $this->models)) {
            return false;
        }
        $sm = new $model;
        $searchable = $sm->getSearchableColumns();
        if (!is_a($this->client, '\Elasticsearch\Client') || !\App\Helpers\ElasticSearchClientHelper::clientCanConnect($this->client)) {
            $ids = $this->searchEloquentModelForResults($term, $model, $searchable);
        } else {
            $ids = $this->searchElasticModelForResults($term, $model, $searchable);
        }
        if (!is_array($ids) || count($ids) <= 0) {
            return false;
        }
        $ids = array_unique($ids);
        $ids = array_map('intval', $ids);
        $models = $model::find($ids);
        if (is_array($return)) {
            $return = $models;
        } else {
            $return = $return->merge($models);
        }
    }

    public function searchAllElasticModelsForResults($term, $models, &$return = [])
    {
        $models = array_filter($models, [$this, 'returnOnlyModels']);
        $models = array_filter($models, [$this, 'returnOnlySearchableModels']);
        $query = [
            'bool' => [
                'must' => [
                    [
                        'terms' => [
                            'model.keyword' => $models,
                        ],
                    ]
                ],
                'should' => [],
                'minimum_should_match' => 1,
                'boost' => 1.0,
            ],
        ];
        foreach ($models as $model) {
            $sm = new $model;
            $searchable = $sm->getSearchableColumns();
            $sub_query = [
                'bool' => [
                    'must' => [
                        [
                            'terms' => [
                                'model.keyword' => $models,
                            ],
                        ]
                    ],
                    'should' => [],
                    'minimum_should_match' => 1,
                    'boost' => 1.0,
                ],
            ];
            foreach ($searchable as $field) {
                if ('email' == $field) {
                    array_push($sub_query['bool']['should'], [
                        'match_phrase' => [
                            $field => strtolower($term),
                        ],
                    ]);
                } else {
                    array_push($sub_query['bool']['should'], [
                        'match_phrase' => [
                            sprintf('%s.keyword', $field) => $term,
                        ],
                    ]);
                }
            }
            array_push($query['bool']['should'], $sub_query);
        }
        try {
            $es_results = $this->client->search([
                'index' => config('app.es.index'),
                'type' => 'model',
                'body' => [
                    'query' => $query
                ],
            ]);
        } catch (\Exception $e) {
            $es_results = json_decode($e->getMessage(), true);
        }
        $items = [];
        if (is_array($es_results)
            && array_key_exists('hits', $es_results)
            && is_array($es_results['hits'])
            && array_key_exists('total', $es_results['hits'])
            && intval($es_results['hits']['total'] >= 1)
        ) {
            foreach ($es_results['hits']['hits'] as $esmodel) {
                if (array_key_exists('_source', $esmodel)
                    && is_array($esmodel['_source'])
                    && array_key_exists('model_id', $esmodel['_source'])
                ) {
                    $model = $esmodel['_source']['model'];
                    $id = intval($esmodel['_source']['model_id']);
                    $item = $model::find($id);
                    array_push($items, $item);
                }
            }
        }
        $items = collect($items);
        if (is_array($return)) {
            $return = $items;
        } else {
            $return = $return->merge($items);
        }
    }

    protected function searchEloquentModelForResults($term, $model, array $searchable = [])
    {
        $query = null;
        foreach ($searchable as $field) {
            if (is_null($query)) {
                $query = $model::where($field, 'like', $term);
            } else {
                $query->orWhere($field, 'like', $term);
            }
        }
        return $query->pluck('id')->toArray();
    }

    protected function searchElasticModelForResults($term, $model, array $searchable = [])
    {
        $return = [];
        $query = [
            'bool' => [
                'must' => [
                    [
                        'term' => [
                            'model.keyword' => $model,
                        ],
                    ]
                ],
                'should' => [],
                'minimum_should_match' => 1,
                'boost' => 1.0,
            ],
        ];
        foreach ($searchable as $field) {
            if ('email' == $field) {
                array_push($query['bool']['should'], [
                    'match_phrase' => [
                        $field => strtolower($term),
                    ],
                ]);
            } else {
                array_push($query['bool']['should'], [
                    'match_phrase' => [
                        sprintf('%s.keyword', $field) => $term,
                    ],
                ]);
            }
        }
        try {
            $es_results = $this->client->search([
                'index' => config('app.es.index'),
                'type' => 'model',
                'body' => [
                    'query' => $query
                ],
            ]);
        } catch (\Exception $e) {
            $es_results = json_decode($e->getMessage(), true);
        }
        if (is_array($es_results)
            && array_key_exists('hits', $es_results)
            && is_array($es_results['hits'])
            && array_key_exists('total', $es_results['hits'])
            && intval($es_results['hits']['total'] >= 1)
        ) {
            foreach ($es_results['hits']['hits'] as $esmodel) {
                if (array_key_exists('_source', $esmodel)
                    && is_array($esmodel['_source'])
                    && array_key_exists('model_id', $esmodel['_source'])
                ) {
                    array_push($return, intval($esmodel['_source']['model_id']));
                }
            }
        }
        return $return;
    }

    protected function returnOnlyModels($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        return (is_subclass_of($class, 'Illuminate\Database\Eloquent\Model'));
    }

    protected function returnOnlySearchableModels($class)
    {
        $uses = class_uses($class);
        $keys = array_keys($uses);
        $vals = array_values($uses);
        $traits = array_map([$this, 'getTraitWithoutNamespace'], $keys, $vals);
        $traits = array_unique($traits);
        return (in_array('ElasticSearchable', $traits));
    }

    protected function getTraitWithoutNamespace($trait)
    {
        if (false === $lp = strrpos($trait, '\\')) {
            return $trait;
        }
        return substr($trait, $lp + 1);
    }

    protected function canUseElasticSearch()
    {
        return (is_a($this->client, '\Elasticsearch\Client') && \App\Helpers\ElasticSearchClientHelper::clientCanConnect($this->client));
    }

    public static function search($term, array $models = [])
    {
        $return = [];
        $c = get_called_class();
        $obj = new $c;
        if ($obj->canUseElasticSearch()) {
            $obj->searchAllElasticModelsForResults($term, $models, $return);
        } else {
            foreach ($models as $model) {
                $obj->searchModelForResults($term, $model, $return);
            }
        }
        return $return;
    }
}
