<?php

namespace App\Helpers;

trait ElasticSearchable
{
    public function getSearchableColumns()
    {
        return (!is_array($this->searchable_columns)) ? [] : $this->searchable_columns;
    }

    public function getPreviewName()
    {
        $name = '';
        switch (true) {
            case (!is_null($this->name)):
                $name = $this->name;
                break;

            case (!is_null($this->email)):
                $name = $this->email;
                break;

            case (!is_null($this->phone)):
                $name = $this->phone;
                break;

            case (!is_null($this->id)):
                $name = sprintf('#%s', $this->id);
                break;
        }
        return $name;
    }

    public function getUrl()
    {
        return '#';
    }
}
