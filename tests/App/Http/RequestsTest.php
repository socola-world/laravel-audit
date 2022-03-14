<?php

namespace SocolaDaiCa\LaravelAudit\Tests\App\Http;

use Illuminate\Validation\Validator;
use SocolaDaiCa\LaravelAudit\Audit\AuditRequest;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class RequestsTest extends TestCase
{
    protected array $typeDontTogethers = [
        ['image', 'mimes'],
        ['required', 'nullable'],
        ['required', 'sometimes'],
        ['numeric', 'file', 'string', 'array', 'integer', 'email', 'password'],
    ];

    /**
     * @dataProvider requestDataProvider
     */
    public function testRulesDontTogether(AuditRequest $auditRequest)
    {
        foreach ($auditRequest->getRules() as $inputName => $inputRules) {
            foreach ($this->typeDontTogethers as $typeDontTogether) {
                $intersect = array_intersect(
                    $typeDontTogether,
                    array_values(array_filter($inputRules, fn ($item) => is_string($item)))
                );

                static::assertLessThanOrEqual(
                    1,
                    count($intersect),
                    $this->error(
                        $auditRequest->reflectionClass->getName(),
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
     */
    public function testMissingAttributes(AuditRequest $auditRequest): void
    {
        $rules = $auditRequest->getRules();
        $attributes = $auditRequest->attributes();
        $attributes = array_filter($attributes, function ($item) {
            return is_string($item) && !empty($item);
        });
        $missingAttributes = array_diff(
            array_keys($rules),
            array_keys($attributes)
        );
        $missingAttributes = array_values($missingAttributes);

        static::assertCount(
            0,
            $missingAttributes,
            $this->error(
                $auditRequest->reflectionClass->getName().'::attributes() missing',
                $missingAttributes
            )
        );
    }

    protected $ruleKeysShouldNotExists = [
        //        'id', // đang suy nghĩ
        'updated_at',
        'created_at',
    ];

    /**
     * @dataProvider requestDataProvider
     */
    public function testRulesKeyShouldNotExists(AuditRequest $auditRequest)
    {
        $ruleKeysShouldNotExists = array_intersect(
            $this->ruleKeysShouldNotExists,
            array_keys($auditRequest->getRules())
        );

        static::assertEmpty(
            $ruleKeysShouldNotExists,
            $this->error(
                $auditRequest->reflectionClass->getName().'::rules()',
                'remove key',
                $ruleKeysShouldNotExists
            )
        );
    }

    /**
     * @dataProvider requestDataProvider
     */
    public function testDuplicateRule(AuditRequest $auditRequest)
    {
        $duplicateRuleNamesByAttribute = [];

        foreach ($auditRequest->getRulesParse() as $attribute => $rulesParse) {
            $rulenames = $rulesParse;
            $rulenames = array_map(fn (array $item) => $item[0], $rulenames);
            $rulenames = array_map(fn ($item) => is_object($item) ? get_class($item) : $item, $rulenames);
            $rulenames = array_values($rulenames);
            $duplicateRuleNames = array_unique(
                array_diff_assoc(
                    $rulenames,
                    array_unique($rulenames)
                )
            );
            $duplicateRuleNames = array_values($duplicateRuleNames);
            $duplicateRuleNames = array_diff($duplicateRuleNames, ['Exists']);

            if (!$duplicateRuleNames) {
                continue;
            }

            $duplicateRuleNamesByAttribute[$attribute] = $duplicateRuleNames;
        }

        static::assertEmpty(
            $duplicateRuleNamesByAttribute,
            $this->error(
                $auditRequest->reflectionClass->getName().'::rules()',
                'duplicate rule',
                $duplicateRuleNamesByAttribute
            )
        );
    }

    protected $ruleKeysNeedCustomValue = [
        'ProhibitedIf',
        'ProhibitedUnless',
        'RequiredIf',
        'RequiredUnless',
    ];

    /**
     * @dataProvider requestDataProvider
     */
    public function testCustomValues(AuditRequest $auditRequest): void
    {
        $rulesMissingCustomValue = [];

        foreach ($auditRequest->getRulesParse() as $attribute => $ruleParses) {
            foreach ($ruleParses as $ruleParse) {
                [$ruleName, $parameters] = $ruleParse;

                if (in_array($ruleName, $this->ruleKeysNeedCustomValue)) {
                    $rulesMissingCustomValue[$parameters[0]][$parameters[1]] = '?_?';

                    break;
                }
            }
        }

        static::assertEmpty(
            $rulesMissingCustomValue,
            $this->error(
                $auditRequest->reflectionClass->getName(),
                'missing custom value',
                '
{
    //...
    public function withValidator($validator)
    {
        return $validator->addCustomValues('
            .$this->varExport($rulesMissingCustomValue, 2)
        .');
    }
}',
            )
        );
    }

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

    //'String', 'Boolean', 'Date'
    protected $ruleTypes = [
        'Array',
        'Date',
        'Email',
        'Integer',
        'Numeric',
        'String',
    ];

    /**
     * @dataProvider requestDataProvider
     */
    public function testRuleMissingType(AuditRequest $auditRequest)
    {
        $rulesMissingType = [];

        foreach ($auditRequest->getRulesParse() as $attribute => $ruleParses) {
            $isMissing = true;

            foreach ($ruleParses as $ruleParse) {
                [$ruleName, $parameters] = $ruleParse;

                if (in_array($ruleName, $this->ruleTypes)) {
                    $isMissing = false;

                    break;
                }
            }

            if ($isMissing) {
                $rulesMissingType[] = $attribute;
            }
        }

        static::assertEmpty(
            $rulesMissingType,
            $this->error(
                $auditRequest->reflectionClass->getName().'::rules()',
                'key missing type',
                $rulesMissingType,
            )
        );
    }
}