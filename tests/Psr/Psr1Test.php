<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Psr;

use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class Psr1Test extends TestCase
{
    public function test_constants_upper_snake_case()
    {
        $invalidConstants = $this->getReflectionConstants()
            ->filter(function (\ReflectionClassConstant $constant) {
                return preg_match('/^[A-Z_][A-Z0-9_]+$/', $constant->getName());
            })
            ->map(function (\ReflectionClassConstant $constant) {
                return "{$constant->getDeclaringClass()}::{$constant->getName()}";
            })
            ->toArray()
        ;

        static::assertEmpty(
            $invalidConstants,
            "Constant should UPPER_SNAKE_CASE: ".json_encode($invalidConstants, JSON_PRETTY_PRINT)
        );
    }
}
