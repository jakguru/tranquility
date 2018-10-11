<?php

namespace App\Helpers;

// use \App\Helpers\Receivable;

trait Receivable
{
    public function getDisplay()
    {
        $display = '';
        if (strlen($this->name) > 0) {
            $display = $this->name;
        } else {
            $name = trim(sprintf('%s %s', $this->fName, $this->lName));
            if (strlen($name) > 0) {
                $display = $name;
            } elseif (strlen($this->email) > 0) {
                $display = $this->email;
            }
        }
        if (strlen($display) < 1) {
            $display = $this->id;
        }
        return $display;
    }

    public function meetings()
    {
        return $this->morphMany('App\Meeting', 'participant');
    }
}
