<?php

namespace App\Helpers;

// use \App\Helpers\Listable;

trait Listable
{
    public static function getListColumns()
    {
        $return = [];
        if (is_array(self::$list_columns)) {
            $return = array_replace_recursive($return, self::$list_columns);
        } else {
            $return['id'] = [
                'type' => 'integer',
                'label' => 'ID',
            ];
        }
        return $return;
    }
}
