<?php

namespace SocolaDaiCa\LaravelAudit;

use Illuminate\Container\Container;

trait FormRequestTrait
{
    public function failedValidation($validator)
    {
    }

    public function authorize()
    {
        return true;
    }

    public function getValidator()
    {
        return $this->getValidatorInstance();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function __get($key)
    {
        return new Optional(parent::__get($key));
    }
}
