<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ModelListHelper
{
    protected $model;
    protected $request;

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

    public function getAJAXReturn()
    {
        $ret = new \stdClass();
        $ret->items = [];
        $ret->total_items = 0;
        $ret->pagination = new \stdClass();
        $ret->pagination->page = 0;
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
            'columns' => [],
            'items' => [],
            'total_items' => 0,
            'page' => 1,
            'total_pages' => 1,
            'next_page' => 0,
            'previous_page' => 0,
        ];
        return $return;
    }
}
