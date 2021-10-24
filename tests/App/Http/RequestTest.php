<?php

namespace SocolaDaiCa\LaravelAudit\Tests\App\Http;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class RequestTest extends TestCase
{
    /**
     * @dataProvider requestDataProvider
     */
    public function test_mising_attributes(\ReflectionClass $requestReflectionClass, $request): void
    {
        $rules = $request->rules();
        $attributes = $request->attributes();
        $missingAttributes = array_diff(
            array_keys($rules),
            array_keys($attributes)
        );
        $missingAttributes = array_values($missingAttributes);

        $this->assertCount(
            0,
            $missingAttributes,
            $this->echo($requestReflectionClass->getName(), "missing attributes", $missingAttributes)
        );
    }

    protected $ruleCompares = [
        'between',
        'gt',
        'gte',
        'lt',
        'lte',
        'max',
        'min',
        'size',
    ];

    protected $typeOfRuleCompares = [
        'numeric',
        'file',
        'string',
        'array',
    ];
    /**
     * @dataProvider requestDataProvider
     */
    public function test_rule_compare_missing_type(
        \ReflectionClass $requestReflectionClass,
        $request,
        Validator $validator
    ): void {
        $inputsMissingType = [];

        foreach ($validator->getRules() as $inputName => $inputRules) {
            if (preg_grep('/^(between|gt|gte|lt|lte|max|min|size):/', $inputRules) == false) {
                continue;
            }

            if (preg_grep('/^(numeric|file|string|array):/', $inputRules) == true) {
                continue;
            }

            $inputsMissingType[] = $inputName;
        }

        $this->assertEmpty(
            $inputsMissingType,
            $this->echo($requestReflectionClass->getName(), "input missing type", $inputsMissingType)
        );
    }
}
