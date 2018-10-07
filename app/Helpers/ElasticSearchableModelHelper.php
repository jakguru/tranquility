<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class ElasticSearchableModelHelper
{
    public function save(Model $model)
    {
        \App\Jobs\SaveElasticSearchableModel::dispatch($model, 'create');
    }

    public function update(Model $model)
    {
        $searchable = $model->getSearchableColumns();
        $updatable = false;
        foreach ($searchable as $field) {
            if (false == $updatable) {
                $updatable = $model->wasChanged($field);
            }
        }
        if ($updatable == true) {
            \App\Jobs\SaveElasticSearchableModel::dispatch($model, 'update');
        }
    }

    public static function getElasticSearchableModels()
    {
        $path = app_path();
        $models = [];
        try {
            $df = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($df as $item) {
                if ($item->isReadable() && $item->isFile() && mb_strtolower($item->getExtension()) === 'php') {
                    $class = str_replace("/", "\\", mb_substr($item->getRealPath(), mb_strlen($path), -4));
                    $class = sprintf('%s%s', ('\\' == substr('App\\', -1)) ? substr('App\\', 0, strlen('App\\') - 1) : 'App\\', $class);
                    array_push($models, $class);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
        $models = array_filter($models, [get_called_class(), 'returnOnlyModels']);
        $models = array_filter($models, [get_called_class(), 'modelIsSearchable']);
        $names = array_map([get_called_class(), 'getTraitWithoutNamespace'], $models);
        $names = array_map('str_plural', $names);
        return array_combine($models, $names);
    }

    protected static function returnOnlyModels($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        return (is_subclass_of($class, 'Illuminate\Database\Eloquent\Model'));
    }

    protected static function modelIsSearchable($model)
    {
        $trait = 'ElasticSearchable';
        $uses = class_uses($model);
        $keys = array_keys($uses);
        $vals = array_values($uses);
        $traits = array_map([get_called_class(), 'getTraitWithoutNamespace'], $keys, $vals);
        $traits = array_unique($traits);
        return (in_array($trait, $traits));
    }

    protected static function getTraitWithoutNamespace($trait)
    {
        if (false === $lp = strrpos($trait, '\\')) {
            return $trait;
        }
        return substr($trait, $lp + 1);
    }
}
