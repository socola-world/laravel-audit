<?php

namespace SocolaDaiCa\LaravelAudit;

class Optional extends \Illuminate\Support\Optional
{
    public function __get($key)
    {
        return new static(parent::__get($key));
    }

    public function __toString()
    {
        return '';
    }
}
