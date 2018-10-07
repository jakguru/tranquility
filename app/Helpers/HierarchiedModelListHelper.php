<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Helpers\ElasticSearchClientHelper;
use \App\Helpers\PermissionsHelper;
use Request as R;
use URL as U;
use \Auth;

class HierarchiedModelListHelper extends ModelListHelper
{
    public function __construct($model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
        $this->page = $this->request->has('page') ? intval($this->request->input('page')) : 1;
        if ($this->page < 1) {
            $this->page = 1;
        }
        $this->populateTotals();
        if ($this->total_items > 100
            || $this->request->has('filter')
            || ($this->request->has('s') && strlen($this->request->input('s')) > 0)
            || $this->request->has('sort')
        ) {
            $this->populateCollection();
        } else {
            $this->populateFullCollection();
            $this->organizeFullCollectionLabels();
        }
    }

    protected function organizeFullCollectionLabels()
    {
        $hierarchied_field = sprintf('%s_id', $this->getSingularLabel());
        if (!$this->request->has('sort')) {
            // only applies to unsorted list
            $sorted = [];
            foreach ($this->items as $item) {
                if (is_null($item->{$hierarchied_field})) {
                    $item_arr = $item->toArray();
                    $item_arr['children'] = [];
                    $this->appendChildrenToParent($item_arr, $this->items);
                    array_push($sorted, $item_arr);
                }
            }
            $new_item_collection = [];
            foreach ($sorted as $model) {
                $item = $this->items->find($model['id']);
                array_push($new_item_collection, $item);
                foreach ($model['children'] as $child) {
                    $this->appendHierarchyToModelLabel($child, 1, $new_item_collection);
                }
            }
            $this->items = collect($new_item_collection);
        }
    }

    protected function appendHierarchyToModelLabel($model, $depth = 1, &$collection)
    {
        $new_name = str_repeat("     ", $depth);
        $new_name .= '⊢ ';
        $new_name .= $model['name'];
        $item = $this->items->find($model['id']);
        $item->name = $new_name;
        array_push($collection, $item);
        foreach ($model['children'] as $child) {
            $this->appendHierarchyToModelLabel($child, $depth + 1, $collection);
        }
    }

    protected function appendChildrenToParent(&$parent, $items)
    {
        $hf = sprintf('%s_id', $this->getSingularLabel());
        foreach ($items as $item) {
            if ($item->{$hf} == $parent['id']) {
                $item_arr = $item->toArray();
                $item_arr['children'] = [];
                $this->appendChildrenToParent($item_arr, $items);
                array_push($parent['children'], $item_arr);
            }
        }
    }

    protected function populateFullCollection()
    {
        $es = ElasticSearchClientHelper::getClient();
        if (PermissionsHelper::modelHasTrait($this->model, 'ElasticSearchable')
            && is_a($es, '\Elasticsearch\Client')
            && ElasticSearchClientHelper::clientCanConnect($es)
        ) {
            $esq = [
                'index' => config('app.es.index'),
                'type' => 'model',
                'body' => [
                    'from' => 0,
                    'size' => 100,
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
                        switch (true) {
                            case (array_key_exists('min', $value) && array_key_exists('max', $value)):
                                $min = $value['min'];
                                $max = $value['max'];
                                if (in_array($this->getFieldType($key), ['date', 'time', 'datetime'])) {
                                    // make a carbon object assuming the date is in the user's local datetime
                                    $min = $this->request->user()->getDateTimeAsUserTimezone($min);
                                    $max = $this->request->user()->getDateTimeAsUserTimezone($max);
                                    // convert the date to the system timezone
                                    if (!is_null($min)) {
                                        $min->setTimezone(config('app.timezone'));
                                    }
                                    if (!is_null($max)) {
                                        $max->setTimezone(config('app.timezone'));
                                    }
                                }
                                $range = [];
                                if (!is_null($min)) {
                                    $range['gte'] = $min->toDateTimeString();
                                }
                                if (!is_null($max)) {
                                    $range['lte'] = $max->toDateTimeString();
                                }
                                if (count($range) > 0) {
                                    array_push($esq['body']['query']['bool']['must'], ['range' => [$key => $range]]);
                                }
                                break;

                            case (!self::isAssociativeArray($value) && count($value) > 0):
                                if (in_array($this->getFieldType($key), ['integer', 'email'])) {
                                    array_push($esq['body']['query']['bool']['must'], ['terms' => [$key => $value]]);
                                } else {
                                    array_push($esq['body']['query']['bool']['must'], ['terms' => [sprintf('%s.keyword', $key) => $value]]);
                                }
                                break;
                        }
                    }
                }
            }
            if ($this->request->has('s') && strlen($this->request->input('s')) > 0) {
                $esq['body']['query']['bool']['should'] = [];
                $esq['body']['query']['bool']['minimum_should_match'] = 1;
                $esq['body']['query']['bool']['boost'] = 1.0;
                $sm = new $this->model;
                $searchable = (PermissionsHelper::modelHasTrait($this->model, 'ElasticSearchable')) ? $sm->getSearchableColumns() : ['name'];
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
            /**
             * TODO: If the item is an ownable item, make sure to only retrieve items which are owned by the requesting user!
             */
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
            }
            $this->items = collect($items);
        } else {
            $m = $this->model;
            $query = $m::limit(100)->offset(0);
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
                                $query->where([$key => $value]);
                                break;
                        }
                    } elseif (is_array($value)) {
                        switch (true) {
                            case (array_key_exists('min', $value) && array_key_exists('max', $value)):
                                $min = $value['min'];
                                $max = $value['max'];
                                if (in_array($this->getFieldType($key), ['date', 'time', 'datetime'])) {
                                    // make a carbon object assuming the date is in the user's local datetime
                                    $min = $this->request->user()->getDateTimeAsUserTimezone($min);
                                    $max = $this->request->user()->getDateTimeAsUserTimezone($max);
                                    // convert the date to the system timezone
                                    if (!is_null($min)) {
                                        $min->setTimezone(config('app.timezone'));
                                    }
                                    if (!is_null($max)) {
                                        $max->setTimezone(config('app.timezone'));
                                    }
                                }
                                if (strlen($min) > 0 && strlen($max) > 0) {
                                    $query->whereBetween($key, [$min, $max]);
                                } elseif (!is_null($min) && strlen($min) > 0) {
                                    $query->where($key, '>=', $min);
                                } elseif (!is_null($max) && strlen($max) > 0) {
                                    $query->where($key, '<=', $max);
                                }
                                break;

                            case (!self::isAssociativeArray($value) && count($value) > 0):
                                $query->whereIn($key, $value);
                                break;
                        }
                    }
                }
            }
            if ($this->request->has('s') && strlen($this->request->input('s')) > 0) {
                $sm = new $this->model;
                $searchable = (PermissionsHelper::modelHasTrait($this->model, 'ElasticSearchable')) ? $sm->getSearchableColumns() : ['name'];
                foreach ($searchable as $field) {
                    $query->orWhere($field, 'like', $this->request->input('s'));
                }
            }
            if ($this->request->has('sort')) {
                foreach ($this->request->input('sort') as $key => $direction) {
                    $query->orderBy($key, $direction);
                }
            } else {
                $query->orderBy('id', 'desc');
            }
            /**
             * TODO: If the item is an ownable item, make sure to only retrieve items which are owned by the requesting user!
             */
            $this->items = $query->get();
        }
    }

    protected function populateTotals()
    {
        $es = ElasticSearchClientHelper::getClient();
        if (PermissionsHelper::modelHasTrait($this->model, 'ElasticSearchable')
            && is_a($es, '\Elasticsearch\Client')
            && ElasticSearchClientHelper::clientCanConnect($es)
        ) {
            $esq = [
                'index' => config('app.es.index'),
                'type' => 'model',
                'body' => [
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
                        switch (true) {
                            case (array_key_exists('min', $value) && array_key_exists('max', $value)):
                                $min = $value['min'];
                                $max = $value['max'];
                                if (in_array($this->getFieldType($key), ['date', 'time', 'datetime'])) {
                                    // make a carbon object assuming the date is in the user's local datetime
                                    $min = $this->request->user()->getDateTimeAsUserTimezone($min);
                                    $max = $this->request->user()->getDateTimeAsUserTimezone($max);
                                    // convert the date to the system timezone
                                    if (!is_null($min)) {
                                        $min->setTimezone(config('app.timezone'));
                                    }
                                    if (!is_null($max)) {
                                        $max->setTimezone(config('app.timezone'));
                                    }
                                }
                                $range = [];
                                if (!is_null($min)) {
                                    $range['gte'] = $min->toDateTimeString();
                                }
                                if (!is_null($max)) {
                                    $range['lte'] = $max->toDateTimeString();
                                }
                                if (count($range) > 0) {
                                    array_push($esq['body']['query']['bool']['must'], ['range' => [$key => $range]]);
                                }
                                break;

                            case (!self::isAssociativeArray($value) && count($value) > 0):
                                if (in_array($this->getFieldType($key), ['integer', 'email'])) {
                                    array_push($esq['body']['query']['bool']['must'], ['terms' => [$key => $value]]);
                                } else {
                                    array_push($esq['body']['query']['bool']['must'], ['terms' => [sprintf('%s.keyword', $key) => $value]]);
                                }
                                break;
                        }
                    }
                }
            }
            if ($this->request->has('s') && strlen($this->request->input('s')) > 0) {
                $esq['body']['query']['bool']['should'] = [];
                $esq['body']['query']['bool']['minimum_should_match'] = 1;
                $esq['body']['query']['bool']['boost'] = 1.0;
                $sm = new $this->model;
                $searchable = (PermissionsHelper::modelHasTrait($this->model, 'ElasticSearchable')) ? $sm->getSearchableColumns() : ['name'];
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
            /**
             * TODO: If the item is an ownable item, make sure to only retrieve items which are owned by the requesting user!
             */
            try {
                $es_results = $es->count($esq);
            } catch (\Exception $e) {
                $es_results = json_decode($e->getMessage(), true);
            }
            $items = [];
            if (is_array($es_results)
                && array_key_exists('count', $es_results)
            ) {
                $this->total_items = intval($es_results['count']);
            }
        } else {
            $m = $this->model;
            $countQuery = null;
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
                                if (is_null($countQuery)) {
                                    $m = $this->model;
                                    $countQuery = $m::where([$key => $value]);
                                } else {
                                    $countQuery->where([$key => $value]);
                                }
                                break;
                        }
                    } elseif (is_array($value)) {
                        switch (true) {
                            case (array_key_exists('min', $value) && array_key_exists('max', $value)):
                                $min = $value['min'];
                                $max = $value['max'];
                                if (in_array($this->getFieldType($key), ['date', 'time', 'datetime'])) {
                                    // make a carbon object assuming the date is in the user's local datetime
                                    $min = $this->request->user()->getDateTimeAsUserTimezone($min);
                                    $max = $this->request->user()->getDateTimeAsUserTimezone($max);
                                    // convert the date to the system timezone
                                    if (!is_null($min)) {
                                        $min->setTimezone(config('app.timezone'));
                                    }
                                    if (!is_null($max)) {
                                        $max->setTimezone(config('app.timezone'));
                                    }
                                }
                                if (strlen($min) > 0 && strlen($max) > 0) {
                                    if (is_null($countQuery)) {
                                        $m = $this->model;
                                        $countQuery = $m::whereBetween($key, [$min, $max]);
                                    } else {
                                        $countQuery->whereBetween($key, [$min, $max]);
                                    }
                                } elseif (!is_null($min) && strlen($min) > 0) {
                                    if (is_null($countQuery)) {
                                        $m = $this->model;
                                        $countQuery = $m::where($key, '>=', $min);
                                    } else {
                                        $countQuery->where($key, '>=', $min);
                                    }
                                } elseif (!is_null($max) && strlen($max) > 0) {
                                    if (is_null($countQuery)) {
                                        $m = $this->model;
                                        $countQuery = $m::where($key, '<=', $max);
                                    } else {
                                        $countQuery->where($key, '<=', $max);
                                    }
                                }
                                break;

                            case (!self::isAssociativeArray($value) && count($value) > 0):
                                if (is_null($countQuery)) {
                                    $m = $this->model;
                                    $countQuery = $m::whereIn($key, $value);
                                } else {
                                    $countQuery->whereIn($key, $value);
                                }
                                break;
                        }
                    }
                }
            }
            if ($this->request->has('s') && strlen($this->request->input('s')) > 0) {
                $sm = new $this->model;
                $searchable = (PermissionsHelper::modelHasTrait($this->model, 'ElasticSearchable')) ? $sm->getSearchableColumns() : ['name'];
                foreach ($searchable as $field) {
                    if (is_null($countQuery)) {
                        $m = $this->model;
                        $countQuery = $m::where($field, $this->request->input('s'));
                    } else {
                        $countQuery->orWhere($field, $this->request->input('s'));
                    }
                }
            }
            /**
             * TODO: If the item is an ownable item, make sure to only retrieve items which are owned by the requesting user!
             */
            if (is_null($countQuery)) {
                $m = $this->model;
                $this->total_items = $m::all()->count();
            } else {
                $this->total_items = $countQuery->count();
            }
        }
    }

    public function getViewVariables()
    {
        if ($this->total_items > 100) {
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
        } else {
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
                'total_pages' => 1,
                'next_page' => 0,
                'previous_page' => 0,
            ];
        }
        return $return;
    }
}
