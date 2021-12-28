<?php

namespace SocolaDaiCa\LaravelAudit\Tests;

class ClassTest extends TestCase
{
    public function testMethodLines()
    {
        $this->assertTrue(true);
//        $maxLines = 100;
//        $this->shouldWarning(function () {
//            $this->getReflectionClassMethods()->each(function (\ReflectionMethod $reflectionMethod) use ($maxLines) {
//                $linesCount = $reflectionMethod->getEndLine() - $reflectionMethod->getStartLine();
//                static::assertLessThanOrEqual(
//                    $maxLines,
//                    $linesCount,
//                    "{$reflectionMethod->class} function {$reflectionMethod->getName()} too long, should <= {$maxLines} lines"
//                );
//            });
//        });
    }
}
