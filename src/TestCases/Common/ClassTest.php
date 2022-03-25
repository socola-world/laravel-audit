<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\Common;

use SocolaDaiCa\LaravelAudit\Audit\Audit1;
use SocolaDaiCa\LaravelAudit\Audit\AuditClass;
use SocolaDaiCa\LaravelAudit\TestCases\TestCase;
use function collect;

class ClassTest extends TestCase
{
    /**
     * @dataProvider classDataProvider
     */
    public function testMethodLines(AuditClass $auditClass)
    {
        $maxLines = 50;
        $methodsTooLong = [];
        collect($auditClass->reflectionClass->getMethods())
            ->each(function (\ReflectionMethod $reflectionMethod) use ($maxLines, &$methodsTooLong, $auditClass) {
                if ($auditClass->reflectionClass->getFileName() != $reflectionMethod->getFileName()) {
                    return;
                }

                $linesCount = $reflectionMethod->getEndLine() - $reflectionMethod->getStartLine();

                if ($linesCount <= $maxLines) {
                    return;
                }

                $methodsTooLong["{$reflectionMethod->getName()}()"] = $linesCount;
            });

        $this->shouldWarning(function () use ($auditClass, $maxLines, $methodsTooLong) {
            static::assertEmpty(
                $methodsTooLong,
                $this->error(
                    "Method too long, should <= {$maxLines} lines",
                    "\n{$auditClass->reflectionClass->getName()}",
                    $methodsTooLong,
                )
            );
        });
    }

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
