<?php

namespace SocolaDaiCa\LaravelAudit;

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
}
