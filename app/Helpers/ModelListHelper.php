<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ModelListHelper
{
    protected $model;
    protected $request;
    protected $items = [];

    public function __construct($model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
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
        $ret->total_items = 0;
        $ret->pagination = new \stdClass();
        $ret->pagination->page = 1;
        $ret->pagination->total_pages = 0;
        $ret->pagination->next_page = 0;
        $ret->pagination->previous_page = 0;
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
            'items' => [],
            'total_items' => 0,
            'page' => 1,
            'total_pages' => 1,
            'next_page' => 0,
            'previous_page' => 0,
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