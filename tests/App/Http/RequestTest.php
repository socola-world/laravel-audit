<?php

namespace SocolaDaiCa\LaravelAudit\Tests\App\Http;

use Illuminate\Validation\Validator;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class RequestTest extends TestCase
{
    protected $typeDontTogethers = [
        ['image', 'mimes'],
        ['required', 'nullable'],
        ['numeric', 'file', 'string', 'array', 'integer', 'email', 'password'],
    ];

    /**
     * @dataProvider requestDataProvider
     *
     * @param mixed $request
     */
    public function testRulesDontTogether(
        \ReflectionClass $requestReflectionClass,
        $request,
        Validator $validator
    ) {
        foreach ($validator->getRules() as $inputName => $inputRules) {
            foreach ($this->typeDontTogethers as $typeDontTogether) {
                $intersect = array_intersect(
                    $typeDontTogether,
                    array_values(array_filter($inputRules, fn ($item) => is_string($item)))
                );

                static::assertLessThanOrEqual(
                    1,
                    count($intersect),
                    $this->error(
                        $requestReflectionClass->getName(),
                        $inputName,
                        'dont put rules',
                        $intersect,
                        'together',
                    )
                );
            }
        }
    }

    /**
     * @dataProvider requestDataProvider
     *
     * @param mixed $request
     */
    public function testMisingAttributes(\ReflectionClass $requestReflectionClass, $request): void
    {
        $rules = $request->rules();
        $attributes = $request->attributes();
        $missingAttributes = array_diff(
            array_keys($rules),
            array_keys($attributes)
        );
        $missingAttributes = array_values($missingAttributes);

        static::assertCount(
            0,
            $missingAttributes,
            $this->error($requestReflectionClass->getName(), 'missing attributes', $missingAttributes)
        );
    }

//    /**
//     * @dataProvider requestDataProvider
//     */
//    public function test_custom_values(\ReflectionClass $requestReflectionClass, $request): void
//    {
//
//    }

//    protected $ruleCompares = [
//        'between',
//        'gt',
//        'gte',
//        'lt',
//        'lte',
//        'max',
//        'min',
//        'size',
//    ];
//
//    protected $typeOfRuleCompares = [
//        'numeric',
//        'file',
//        'string',
//        'array',
//        'numeric',
//    ];

//    /*
//     * @dataProvider requestDataProvider
//     */
//    public function test_rule_compare_missing_type(
//        \ReflectionClass $requestReflectionClass,
//        $request,
//        Validator $validator
//    ): void {
//        $inputsMissingType = [];
//
//        foreach ($validator->getRules() as $inputName => $inputRules) {
//            if (preg_grep('/^(between|gt|gte|lt|lte|max|min|size|in):/', $inputRules) == false) {
//                continue;
//            }
//
//            if (preg_grep('/^(numeric|file|string|array|integer|email)$/', $inputRules) == true) {
//                continue;
//            }
//
//            $inputsMissingType[] = $inputName;
//        }
//
//        $this->assertEmpty(
//            $inputsMissingType,
//            $this->echo($requestReflectionClass->getName(), "input missing type", $inputsMissingType)
//        );
//    }
}
