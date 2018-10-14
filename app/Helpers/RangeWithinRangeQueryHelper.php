<?php

namespace App\Helpers;

use Carbon\Carbon;

class RangeWithinRangeQueryHelper
{

    use \App\Helpers\DebugLoggable;

    protected $start_carbon;
    protected $end_carbon;
    protected $start_column = 'starts_at';
    protected $end_column = 'ends_at';

    public function __construct($start_carbon, $end_carbon, $start_column = 'starts_at', $end_column = 'ends_at')
    {
        if (is_a($start_carbon, 'Carbon\Carbon')) {
            $this->start_carbon = $start_carbon;
        } else {
            throw new \Exception(sprintf('%s is not an instance of Carbon\Carbon', print_r($start_carbon, true)), 1);
        }
        if (is_a($end_carbon, 'Carbon\Carbon')) {
            $this->end_carbon = $end_carbon;
        } else {
            throw new \Exception(sprintf('%s is not an instance of Carbon\Carbon', print_r($end_carbon, true)), 1);
        }
        if (is_string($start_column)) {
            $this->start_column = $start_column;
        }
        if (is_string($end_column)) {
            $this->end_column = $end_column;
        }
    }

    public function updateQuery(&$query)
    {
        $query->where(function ($query) {
            $query->whereBetween($this->start_column, [$this->start_carbon, $this->end_carbon])
                  ->orWhereBetween($this->end_column, [$this->start_carbon, $this->end_carbon])
                  ->orWhere([
                    [$this->start_column, '<=', $this->start_carbon],
                    [$this->end_column, '>=', $this->end_carbon],
                  ]);
        });
    }

    public static function modifyQuery(&$query, $start_carbon, $end_carbon, $start_column = 'starts_at', $end_column = 'ends_at')
    {
        $c = get_called_class();
        $obj = new $c($start_carbon, $end_carbon, $start_column, $end_column);
        $obj->updateQuery($query);
    }
}
