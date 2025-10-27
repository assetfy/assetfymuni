<?php

namespace App\Traits;

trait SortableTrait
{
    public function order($sort)
    {
        if ($this->sort == $sort) {
            $this->direction = ($this->direction == 'desc') ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'desc';
        }
    }

    public function sortIcon($field)
    {
        if ($this->sort == $field) {
            return $this->direction == 'asc' ? 'fa-sort-alpha-up-alt' : 'fa-sort-alpha-down-alt';
        } else {
            return 'fa-sort';
        }
    }

    public function eventos()
    {
        $this->dispatch('lucky');
        $this->dispatch('refreshLivewireTable');
        $this->dispatch('render');
        $this->open = false;
    }
}