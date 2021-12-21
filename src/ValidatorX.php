<?php

namespace SocolaDaiCa\LaravelAudit;

use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Validation\Rule as RuleContract;

class ValidatorX extends Validator {
    /** @var MessageBag */
    public $errorMessages;

    /** @var array */
    protected $hasExplicitFileErrorMessage;

    protected $explicitFileRules = [
        'File', 'Image', 'Mimes', 'Mimetypes', 'Dimensions',
    ];

    public $errorMessagesV2 = [];

    function availableErrors()
    {
        $this->errorMessages = new MessageBag();
        $this->hasExplicitFileErrorMessage = [];

        foreach($this->rules as $attribute => $rules) {
            $attribute = str_replace('\.', '->', $attribute);
            foreach($rules as $rule) {
                $realRule = $rule;
                [$rule, $parameters] = ValidationRuleParser::parse($rule);

                if($rule == '') {
                    continue;
                }
                if(($keys = $this->getExplicitKeys($attribute)) &&
                    $this->dependsOnOtherFields($rule)) {
                    $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
                }
                // explicitly add "failed to upload" error
                if($this->hasRule($attribute, $this->explicitFileRules) && !in_array($attribute, $this->hasExplicitFileErrorMessage)) {
                    $this->addFailureMessage($realRule, $attribute, 'uploaded', []);
                    $this->hasExplicitFileErrorMessage[] = $attribute;
                }

                if($rule instanceof RuleContract) {
                    $messages = $rule->message() ? (array)$rule->message() : [get_class($rule)];
                    foreach($messages as $message) {
                        $this->addFailureMessage($realRule, $attribute, get_class($rule), [], $message);
                    }
                } else {
                    $this->addFailureMessage($realRule, $attribute, $rule, $parameters);
                }
            }
        }

//        return $this->errorMessages;
        return $this->errorMessagesV2;
    }

    function addFailureMessage($realRule, $attribute, $rule, $parameters = [], $rawMessage = null)
    {
        if (
            in_array(
                $rawMessage ?? $this->getMessage($attribute, $rule),
                [
                    "validation.nullable",
                    "validation.exclude_if",
                    'validation.sometimes',
                ]
            )
        ) {
            return;
        }

        if ($rule == 'RequiredIf') {
            data_set($this->data, $parameters[0], $parameters[1]);
        }

        $m = $this->makeReplacements(
            $rawMessage ?? $this->getMessage($attribute, $rule), $attribute, $rule, $parameters
        );

////        $message, $attribute, $rule, $parameters
//        if ($rule == 'RequiredIf') {
////            dd($realRule, $this->getMessage($attribute, $rule), $this->customValues, $m);
////            dd($this->customValues);
////            dd($realRule, $rule, $m, $rawMessage, $this->getMessage($attribute, $rule));
////            dd('sss');
//        }

        $this->errorMessagesV2[$attribute] = $this->errorMessagesV2[$attribute] ?? [];

        if ($realRule instanceof RuleContract) {
            $realRule = $rule;
        }

        $this->errorMessagesV2[$attribute][$realRule] = $m;

        $this->errorMessages->add($attribute, $this->makeReplacements(
            $rawMessage ?? $this->getMessage($attribute, $rule), $attribute, $rule, $parameters
        ));
    }

    // we have to override this method since file-type errors depends on data value rather than rule type
    protected function getAttributeType($attribute)
    {
        if($this->hasRule($attribute, $this->explicitFileRules)) {
            return 'file';
        }
        return parent::getAttributeType($attribute);
    }

    public static function make(Validator $v, \Illuminate\Foundation\Http\FormRequest $request)
    {
        $translator = app(\Illuminate\Translation\Translator::class);
        $validator = new self(
            $translator,
            [],
            $v->getRules(),
            $v->customMessages,
            $v->customAttributes
        );

        $validator->addReplacers($v->replacers);
        $validator->customValues = $v->customValues;

        return $validator;
    }
}
