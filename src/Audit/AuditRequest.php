<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\Validator;
use SocolaDaiCa\LaravelAudit\FormRequest;
use SocolaDaiCa\LaravelAudit\ValidatorX;

class AuditRequest extends AuditClass
{
    /**
     * @var FormRequest
     */
    protected $request;

    /**
     * @var ValidatorX
     */
    protected $validator;

    /**
     * @return FormRequest
     */
    public function getRequest()
    {
        if ($this->request) {
            return $this->request;
        }

        $requestClassName = trim($this->reflectionClass->getName(), '\\');
        $className = str_replace('\\', '__', $requestClassName);

        $class = sprintf(
            'class %s extends %s {
                use SocolaDaiCa\LaravelAudit\FormRequestTrait;
            }',
            $className,
            $requestClassName
        );

        if (class_exists($className) === false) {
            eval($class);
        }

        $this->request = app($className);

        return $this->request;
    }

    public function getValidator(): Validator
    {
        if ($this->validator) {
            return $this->validator;
        }

        return $this->validator = $this->request->getValidator();
    }

    public function getRules()
    {
        return $this->getValidator()->getRules();
    }

    protected $rulesParse;

    public function getRulesParse()
    {
        if ($this->rulesParse) {
            return $this->rulesParse;
        }

        $this->rulesParse = [];

        foreach ($this->getValidator()->getRules() as $attribute => $rules) {
            $this->rulesParse[$attribute] = [];

            foreach ($rules as $rule) {
//                $this->rulesParse[$ruleName]['parse'] = ValidationRuleParser::parse($rule);
                $this->rulesParse[$attribute][] = ValidationRuleParser::parse($rule);
//                $this->rulesParse[$ruleName]['rule'] = $rule;
            }
        }

        return $this->rulesParse;
    }

    public function attributes()
    {
        return $this->getRequest()->attributes();
    }
}
