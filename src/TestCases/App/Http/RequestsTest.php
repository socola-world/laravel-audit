<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\App\Http;

use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use SocolaDaiCa\LaravelAudit\Audit\AuditRequest;
use SocolaDaiCa\LaravelAudit\TestCases\TestCase;
use function collect;

class RequestsTest extends TestCase
{
    protected array $typeDontTogethers = [
        ['image', 'mimes'],
        ['required', 'nullable'],
        ['required', 'sometimes'],
        ['numeric', 'file', 'string', 'array', 'integer', 'email', 'password'],
        ['nullable', 'array'],
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
                    array_values(array_filter($inputRules, fn ($item) => is_string($item))),
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
                    ),
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
            array_keys($attributes),
        );
        $missingAttributes = array_values($missingAttributes);
        $missingAttributes = array_fill_keys($missingAttributes, '?_?');

        static::assertCount(
            0,
            $missingAttributes,
            $this->error(
                $auditRequest->reflectionClass->getName().'::attributes() missing',
                $missingAttributes,
            ),
        );
    }

    protected $ruleKeysShouldNotExists = [
        //        'id', // ??ang suy ngh??
        'updated_at',
        'created_at',
        'created_by',
        'updated_by',
    ];

    /**
     * @dataProvider requestDataProvider
     */
    public function testRulesKeyShouldNotExists(AuditRequest $auditRequest)
    {
        $ruleKeysShouldNotExists = array_intersect(
            $this->ruleKeysShouldNotExists,
            array_keys($auditRequest->getRules()),
        );

        static::assertEmpty(
            $ruleKeysShouldNotExists,
            $this->error(
                $auditRequest->reflectionClass->getName().'::rules()',
                'remove key',
                $ruleKeysShouldNotExists,
            ),
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
                    array_unique($rulenames),
                ),
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
                $duplicateRuleNamesByAttribute,
            ),
        );
    }

    protected array $ruleKeysNeedCustomValue = [
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

                if (in_array($ruleName, $this->ruleKeysNeedCustomValue)
                    && empty($auditRequest->getValidator()->customValues[$parameters[0]][''.$parameters[1]])
                ) {
                    $rulesMissingCustomValue[$parameters[0]][''.$parameters[1]] = '?_?';

                    break;
                }

                if (in_array($ruleName, ['After', 'AfterOrEqual', 'Before', 'BeforeOrEqual'])
                    && in_array($parameters[0], ['today', 'yesterday', 'tomorrow'])
                    && empty($auditRequest->getValidator()->customValues[$parameters[0]][''.$parameters[1]])
                ) {
                    $rulesMissingCustomValue[$parameters[0]][''.$parameters[1]] = '?_?';

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
            ),
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

    // 'String', 'Boolean', 'Date'
    protected array $ruleTypes = [
        'Array',
        'Date',
        'Email',
        'Integer',
        'Numeric',
        'String',
        'File',
        'Json',
        'Password',
    ];

    /**
     * @dataProvider requestDataProvider
     */
    public function testRuleMissingType(AuditRequest $auditRequest)
    {
        $rulesMissingType = [];

        foreach ($auditRequest->getRulesParse() as $attribute => $ruleParses) {
//            [$ruleName, $parameters] = $ruleParse;
            $isMissing = collect($ruleParses)
                ->filter(fn ($ruleParse) => in_array($ruleParse[0], $this->ruleTypes))
                ->count() == 0;

            if ($isMissing) {
                $rulesMissingType[] = $attribute;
            }
        }

        static::assertEmpty(
            $rulesMissingType,
            $this->error(
                $auditRequest->reflectionClass->getName().'::rules()',
                'attribute missing type',
                $rulesMissingType,
            ),
        );
    }

    /**
     * @dataProvider requestDataProvider
     */
    public function testAttributeShouldPlural(AuditRequest $auditRequest)
    {
        $attributeShouldPlurals = [];

        foreach ($auditRequest->getRulesParse() as $attribute => $ruleParses) {
//            [$ruleName, $parameters] = $ruleParse;
            $ruleNames = collect($ruleParses)->map(fn ($e) => $e[0])->values()->toArray();

            if ($auditRequest->isAttributeShouldPlural($attribute) === false) {
                continue;
            }

            $attributeShouldPlurals[] = $attribute;
        }

        static::assertEmpty(
            $attributeShouldPlurals,
            $this->error(
                $auditRequest->reflectionClass->getName().'::rules()',
                'attribute should ',
                $attributeShouldPlurals,
            ),
        );
    }

    protected array $ruleFollowTypes = [
        'After' => ['Date'],
        'AfterOrEqual' => ['Date'],
        'Before' => ['Date'],
        'BeforeOrEqual' => ['Date'],
        'DateEquals' => ['Date'],
        'Digits' => ['Numeric', 'Integer'],
        'DigitsBetween' => ['Numeric', 'Integer'],
        'Between' => ['Numeric', 'Integer', 'Array', 'String'],
        'Size' => ['Numeric', 'Integer', 'Array', 'String'],
        'Gt' => ['Numeric', 'Integer', 'Array', 'String'],
        'Gte' => ['Numeric', 'Integer', 'Array', 'String'],
        'String' => ['Required', 'Nullable', 'Min', 'Between'],
        //        'Dimensions' => 'image',
        //        'Dimensions' => 'mine',
    ];

    /**
     * @dataProvider requestDataProvider
     */
    public function testRuleFollowType(AuditRequest $auditRequest)
    {
        $rulesMissingType = [];

        foreach ($auditRequest->getRulesParse() as $attribute => $ruleParses) {
            collect($ruleParses)
                ->each(function ($ruleParse) use (&$rulesMissingType, $attribute, $ruleParses) {
                    [$ruleName, $parameters] = $ruleParse;

                    if (is_object($ruleName)) {
                        return;
                    }

                    if (array_key_exists($ruleName, $this->ruleFollowTypes) === false) {
                        return;
                    }

                    foreach ($ruleParses as $otherRuleParse) {
                        if (in_array($otherRuleParse[0], $this->ruleFollowTypes[$ruleName])) {
                            return;
                        }
                    }

                    $recommendTypes = array_map(fn ($e) => Str::snake($e), $this->ruleFollowTypes[$ruleName]);
                    $recommendTypeString = implode('|', $recommendTypes);
                    $rulesMissingType[$attribute] = $recommendTypeString;
                })
            ;
        }

        static::assertEmpty(
            $rulesMissingType,
            $this->error(
                $auditRequest->reflectionClass->getName().'::rules()',
                'type of attribute should be',
                $rulesMissingType,
            ),
        );
    }
}
