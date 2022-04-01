<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\App\Http\Models;

use JsonException;
use ReflectionException;
use SocolaDaiCa\LaravelAudit\Audit\AuditModel;
use SocolaDaiCa\LaravelAudit\Helper;

trait TraitModelAppendsTest
{
    /**
     * https://github.com/laravel/framework/issues/41704.
     *
     * @throws JsonException
     * @throws ReflectionException
     */
    public function testAppendsSnakeCase(AuditModel $auditModel)
    {
        $columnsWrongFormat = collect($auditModel->getAppends())
            ->filter(fn ($appendItem) => !Helper::isSnakeCase($appendItem))
            ->toArray()
        ;

        static::assertEmpty(
            $columnsWrongFormat,
            $this->error(
                "{$auditModel->reflectionClass->getName()}::\$appends should be snake_case",
                $columnsWrongFormat,
            ),
        );
    }
}
