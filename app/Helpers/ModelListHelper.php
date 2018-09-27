<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Helpers\ElasticSearchClientHelper;

class ModelListHelper
{
    protected $model;
    protected $request;
    protected $items = [];
    protected $total_items = 0;
    protected $page = 0;

    public function __construct($model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
        $this->page = $this->request->has('page') ? intval($this->request->input('page')) : 1;
        if ($this->page < 1) {
            $this->page = 1;
        }
        $this->populateCollection();
    }

    protected function populateCollection()
    {
        $es = ElasticSearchClientHelper::getClient();
        if ((is_a($es, '\Elasticsearch\Client') && ElasticSearchClientHelper::clientCanConnect($es))) {
            $esq = [
                'index' => config('app.es.index'),
                'type' => 'model',
                'body' => [
                    'from' => (($this->page - 1) * config('app.listsize')),
                    'size' => config('app.listsize'),
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => [
                                        'model.keyword' => $this->model,
                                    ],
                                ]
                            ],
                        ],
                    ],
                    'sort' => [],
                ],
            ];
            if ($this->request->has('filter')) {
                foreach ($this->request->input('filter') as $key => $value) {
                    if (!is_array($value) && strlen($value) > 0) {
                        switch ($key) {
                            case 'id':
                                array_push($esq['body']['query']['bool']['must'], ['term' => ['model_id' => intval($value)]]);
                                break;

                            case 'email':
                                array_push($esq['body']['query']['bool']['must'], ['match_phrase' => ['email' => $value]]);
                                break;

                            case 'created_at':
                                # do nothing. Invalid
                                break;

                            case 'updated_at':
                                # do nothing. Invalid
                                break;
                                                        
                            default:
                                array_push($esq['body']['query']['bool']['must'], ['match_phrase' => [sprintf('%s.keyword', $key) => $value]]);
                                break;
                        }
                    } elseif (is_array($value)) {
                    }
                }
            }
            if ($this->request->has('s') && strlen($this->request->input('s')) > 0) {
                $esq['body']['query']['bool']['should'] = [];
                $esq['body']['query']['bool']['minimum_should_match'] = 1;
                $esq['body']['query']['bool']['boost'] = 1.0;
                $sm = new $this->model;
                $searchable = $sm->getSearchableColumns();
                foreach ($searchable as $field) {
                    switch ($field) {
                        case 'id':
                            array_push($esq['body']['query']['bool']['should'], ['term' => ['model_id' => $this->request->input('s')]]);
                            break;

                        case 'email':
                            array_push($esq['body']['query']['bool']['should'], ['match_phrase' => ['email' => strtolower($this->request->input('s'))]]);
                            break;

                        case 'created_at':
                            # do nothing. Invalid
                            break;

                        case 'updated_at':
                            # do nothing. Invalid
                            break;
                                                    
                        default:
                            array_push($esq['body']['query']['bool']['should'], ['match_phrase' => [sprintf('%s.keyword', $field) => $this->request->input('s')]]);
                            break;
                    }
                }
            }
            if ($this->request->has('sort')) {
                foreach ($this->request->input('sort') as $key => $direction) {
                    if ('id' == $key) {
                        $key = 'model_id';
                    }
                    array_push($esq['body']['sort'], [$key => $direction]);
                }
            } else {
                array_push($esq['body']['sort'], ['model_id' => 'desc']);
            }
            try {
                $es_results = $es->search($esq);
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
                $this->total_items = intval($es_results['hits']['total']);
            }
            $this->items = collect($items);
        } else {
            $query = $this->model::limit(config('app.listsize'))->offset(($this->page - 1) * config('app.listsize'));
            if ($this->request->has('filter')) {
                foreach ($this->request->input('filter') as $key => $value) {
                    if (!is_array($value) && strlen($value) > 0) {
                        switch ($key) {
                            case 'created_at':
                                # do nothing. Invalid
                                break;

                            case 'updated_at':
                                # do nothing. Invalid
                                break;
                                                        
                            default:
                                $query->where($key, $value);
                                break;
                        }
                    } elseif (is_array($value)) {
                    }
                }
            }
            if ($this->request->has('s') && strlen($this->request->input('s')) > 0) {
                $sm = new $this->model;
                $searchable = $sm->getSearchableColumns();
                foreach ($searchable as $field) {
                    $query->orWhere($field, 'like', $this->request->input('s'));
                }
            }
            $this->total_items = $query->count();
            if ($this->request->has('sort')) {
                foreach ($this->request->input('sort') as $key => $direction) {
                    $query->orderBy($key, $direction);
                }
            } else {
                $query->orderBy('id', 'desc');
            }
            $this->items = $query->get();
        }
    }

    protected function getSingularLabel()
    {
        $class = $this->model;
        if (false !== $lp = strrpos($class, '\\')) {
            $class = substr($class, $lp + 1);
        }
        $class = strtolower($class);
        return str_singular($class);
    }

    protected function getPluralLabel()
    {
        return str_plural($this->getSingularLabel());
    }

    protected function getBreadcrumbs()
    {
        if (in_array($this->getSingularLabel(), ['user', 'group', 'role'])) {
            return [
                [
                    'name' => config('app.name'),
                    'url' => route('dashboard'),
                ],
                [
                    'name' => __('Settings'),
                    'url' => route('settings'),
                ],
                [
                    'name' => sprintf(__('List of %s'), ucwords($this->getPluralLabel())),
                    'url' => '#',
                ]
            ];
        } else {
            return [
                [
                    'name' => config('app.name'),
                    'url' => route('dashboard'),
                ],
                [
                    'name' => ucwords($this->getPluralLabel()),
                    'url' => '#',
                ]
            ];
        }
    }

    protected function getColumns()
    {
        if (\App\Helpers\PermissionsHelper::modelHasTrait($this->model, 'Listable')) {
            return $this->model::getListColumns();
        }
        return [
            'id' => [
                'type' => 'integer',
                'label' => 'ID',
            ]
        ];
    }

    public function getAJAXReturn()
    {
        $ret = new \stdClass();
        $ret->items = [];
        $ret->total_items = $this->total_items;
        $ret->pagination = new \stdClass();
        $ret->pagination->page = 1;
        $ret->pagination->total_pages = ceil($ret->pagination->total_items / config('app.listsize'));
        $ret->pagination->next_page = ($ret->pagination->page < $ret->pagination->total_pages) ? $ret->pagination->page + 1 : 0;
        $ret->pagination->previous_page = ($ret->pagination->page > 1) ? $ret->pagination->page - 1 : 0;
    }

    public function getViewVariables()
    {
        $return = [
            'title' => ucwords($this->getPluralLabel()),
            'breadcrumbs' => $this->getBreadcrumbs(),
            'single_label' => $this->getSingularLabel(),
            'plural_label' => $this->getPluralLabel(),
            'create_route' => sprintf('create-%s', $this->getSingularLabel()),
            'view_route' => sprintf('view-%s', $this->getSingularLabel()),
            'delete_route' => sprintf('delete-%s', $this->getSingularLabel()),
            'columns' => $this->getColumns(),
            'items' => $this->items,
            'total_items' => $this->total_items,
            'page' => $this->page,
            'total_pages' => (ceil($this->total_items / config('app.listsize')) > 1) ? ceil($this->total_items / config('app.listsize')) : 1,
            'next_page' => ($this->page < ceil($this->total_items / config('app.listsize')) ) ? $this->page + 1 : 0,
            'previous_page' => ($this->page > 1) ? $this->page - 1 : 0,
        ];
        return $return;
    }

    public static function getSortUrl($column, $direction = 'asc')
    {
        $current = URL::current();
        $direction = strtolower($direction);
        if ('none' == $direction) {
            if (false === strpos($current, '?')) {
                return $current;
            } else {
                list($url, $query) = explode('?', $current, 2);
                parse_str($query, $query);
                if (!is_array($query)) {
                    $query = [];
                }
                if (!array_key_exists('sort', $query)) {
                    $query['sort'] = [];
                }
                unset($query['sort'][$column]);
                return sprintf('%s/%s', $url, http_build_query($query));
            }
        }
        $direction = ('desc' == $direction) ? 'desc' : 'asc';
        if (false === strpos($current, '?')) {
            return sprintf('%s?%s', $current, http_build_query([
                'sort' => [$column => $direction],
            ]));
        } else {
            list($url, $query) = explode('?', $current, 2);
            parse_str($query, $query);
            if (!is_array($query)) {
                $query = [];
            }
            if (!array_key_exists('sort', $query)) {
                $query['sort'] = [];
            }
            $query['sort'][$column] = $direction;
            return sprintf('%s/%s', $url, http_build_query($query));
        }
    }
}
