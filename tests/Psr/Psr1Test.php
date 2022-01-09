<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Psr;

use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class Psr1Test extends TestCase
{
    public function testConstantsUpperSnakeCase()
    {
        $invalidConstants = $this->getReflectionConstants()
            ->filter(function (\ReflectionClassConstant $constant) {
                return preg_match('/^[A-Z][A-Z0-9_]+$/', $constant->getName()) === false;
            })
            ->map(function (\ReflectionClassConstant $constant) {
                return "{$constant->getDeclaringClass()->getName()}::{$constant->getName()}";
            })
            ->toArray();

        static::assertEmpty(
            $invalidConstants,
            $this->error(
                'Constant should UPPER_SNAKE_CASE',
                $invalidConstants
            )
        );
    }
}
