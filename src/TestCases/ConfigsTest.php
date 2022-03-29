<?php

namespace SocolaDaiCa\LaravelAudit\TestCases;

use Closure;
use Illuminate\Support\Arr;

class ConfigsTest extends TestCase
{
    public function testKeyName()
    {
        $configKeys = Arr::dot(config()->all());
        $configKeys = array_keys($configKeys);

        $configKeysWrongFormat = collect($configKeys)
            ->filter(fn ($configKey) => preg_match('/^[a-zA-z0-9_-]+\.[a-zA-z0-9_.]+$/', $configKey) == false)
            ->values()
            ->toArray()
        ;

        $this->shouldWarning(fn () => static::assertEmpty(
            $configKeysWrongFormat,
            $this->error(
                'config key should be lower snake_case',
                $configKeysWrongFormat,
            ),
        ));
    }

    public function testValueClosure()
    {
        $configDots = Arr::dot(config()->all());

        $configValuesWrongType = collect($configDots)
            ->filter(fn ($configDot) => $configDot instanceof Closure)
            ->keys()
            ->toArray()
        ;

        static::assertEmpty(
            $configValuesWrongType,
            $this->error(
                'config value should not be Closure',
                $configValuesWrongType,
            ),
        );
    }
}
