<?php

namespace SocolaDaiCa\LaravelAudit;

use Illuminate\Container\Container;

trait FormRequestTrait
{
    protected function failedValidation($validator)
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

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
