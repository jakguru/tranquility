<?php

namespace App\Helpers;

class AppServiceProviderEventHandlerHelper
{
    protected $trait;
    protected $namespace;
    protected $models = [];

    public function __construct($trait = '', $namespace = 'App\\')
    {
        $this->trait = $trait;
        $this->namespace = $namespace;
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
        $this->models = array_filter($this->models, [$this, 'returnOnlyModelsInNamespace']);
        if (strlen($this->trait) > 0) {
            $this->models = array_filter($this->models, [$this, 'returnOnlyModelsWithTrait']);
        }
    }

    public function hookToEvents($callback = '', $events = ['saved'])
    {
        foreach ($events as $event) {
            foreach ($this->models as $model) {
                if (method_exists($model, $event)) {
                    forward_static_call([$model, $event], $callback);
                }
            }
        }
    }

    protected function returnOnlyModelsInNamespace($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        return (is_subclass_of($class, 'Illuminate\Database\Eloquent\Model') && substr($class, 0, strlen($this->namespace)) == $this->namespace);
    }

    protected function returnOnlyModelsWithTrait($class)
    {
        $uses = class_uses($class);
        $keys = array_keys($uses);
        $vals = array_values($uses);
        $traits = array_map([$this, 'getTraitWithoutNamespace'], $keys, $vals);
        $traits = array_unique($traits);
        return (in_array($this->trait, $traits));
    }

    protected function getTraitWithoutNamespace($trait)
    {
        if (false === $lp = strrpos($trait, '\\')) {
            return $trait;
        }
        return substr($trait, $lp + 1);
    }

    public static function hookToTraitedModelEvents($trait = '', $events = ['saved'], $callback = '')
    {
        $c = get_called_class();
        $obj = new $c($trait);
        $obj->hookToEvents($callback, $events);
    }
}
