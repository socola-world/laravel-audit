<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Common;

use SocolaDaiCa\LaravelAudit\Audit\Audit1;
use SocolaDaiCa\LaravelAudit\Audit\AuditClass;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class ClassTest extends TestCase
{
//    public function testMethodLines()
//    {
//        $this->assertTrue(true);
    ////        $maxLines = 100;
    ////        $this->shouldWarning(function () {
    ////            $this->getReflectionClassMethods()->each(function (\ReflectionMethod $reflectionMethod) use ($maxLines) {
    ////                $linesCount = $reflectionMethod->getEndLine() - $reflectionMethod->getStartLine();
    ////                static::assertLessThanOrEqual(
    ////                    $maxLines,
    ////                    $linesCount,
    ////                    "{$reflectionMethod->class} function {$reflectionMethod->getName()} too long, should <= {$maxLines} lines"
    ////                );
    ////            });
    ////        });
//    }

    public function testImports()
    {
        $useStatementsNotFounds = [];
        /*
         * @var AuditClass $auditClass
         */
        foreach ($this->classDataProvider() as $classProvider) {
            $auditClass = $classProvider[0];
            $useStatements = $auditClass->getUseStatements();
            $useStatementsNotFound = array_filter($useStatements, fn ($useStatement) => !Audit1::isClassExist($useStatement));
            $useStatementsNotFound = array_values($useStatementsNotFound);

            if (empty($useStatementsNotFound)) {
                continue;
            }

            $useStatementsNotFounds[$auditClass->reflectionClass->getName()] = $useStatementsNotFound;
        }

        static::assertEmpty(
            $useStatementsNotFounds,
            $this->error(
                '$useStatementsNotFounds = ',
                $useStatementsNotFounds
            )
        );
    }
}
