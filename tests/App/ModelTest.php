<?php

namespace SocolaDaiCa\LaravelAudit\Tests\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class ModelTest extends TestCase
{
    /**
     * @dataProvider modelDataProvider
     */
    public function test_fillable_or_guarded_missing(\ReflectionClass $modelReflection, Model $model, array $columns)
    {
        $columnsNeedFillable = [];
        $columnsNeedGuarded = [];

        foreach ($columns as $column) {
            if ($column->getAutoincrement() !== $model->isFillable($column->getName())) {
                continue;
            }

            if ($column->getAutoincrement()) {
                $columnsNeedGuarded[] = $column->getName();
                continue;
            }

            $needFillable[] = $column->getName();
        }

        static::assertEmpty(
            $columnsNeedFillable,
            "{$modelReflection->getName()} \$fillable missing ".
            json_encode($columnsNeedFillable, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
        );

        static::assertEmpty(
            $columnsNeedGuarded,
            "{$modelReflection->getName()} \$guarded missing ".
            json_encode($columnsNeedGuarded, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
        );
    }

    /**
     * @dataProvider modelDataProvider
     */
    public function test_column_of_fillable_not_exist(\ReflectionClass $modelReflection, Model $model, array $columns)
    {
        $columnsNotExist = array_diff($model->getFillable(), array_keys($columns));
        static::assertEmpty(
            $columnsNotExist,
            $this->echo($modelReflection->getName(), "column of \$fillable not exist in database", $columnsNotExist)
        );
    }

    /**
     * @dataProvider modelDataProvider
     */
    public function test_column_of_guared_not_exist(\ReflectionClass $modelReflection, Model $model, array $columns): void
    {
        $columnsNotExist = array_diff($model->getGuarded(), [...array_keys($columns), '*']);
        static::assertEmpty(
            $columnsNotExist,
            $this->echo($modelReflection->getName(), "column of \$guarded not Exist", $columnsNotExist)
        );
    }

//    public function test_fillable()
//    {
//        $this->getModelReflectionClass()->each(/**
//         * @throws \Doctrine\DBAL\Exception
//         */ function (\ReflectionClass $modelReflection) {
//            $model = $modelReflection->getName();
//            /**
//             * @var Model $instance
//             */
//            $instance = new $model();
//            $columns = Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($instance->getTable());
//            $columnsNeedFillable = [];
//            $columnsNeedGuarded = [];
//
//            foreach ($columns as $column) {
//                if ($column->getAutoincrement() !== $instance->isFillable($column->getName())) {
//                    continue;
//                }
//
//                if ($column->getAutoincrement()) {
//                    $columnsNeedGuarded[] = $column->getName();
//                    continue;
//                }
//
//                $needFillable[] = $column->getName();
//            }
//
//            static::assertEmpty(
//                $columnsNeedFillable,
//                "{$model} \$fillable missing ".
//                json_encode($columnsNeedFillable, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
//            );
//
//            static::assertEmpty(
//                $columnsNeedGuarded,
//                "{$model} \$guarded missing ".
//                json_encode($columnsNeedGuarded, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
//            );
//        });
//    }

    public function test_hidden()
    {
        $columnsShouldHidden = [
            'password',
            'current_password',
            'password',
            'password_confirmation',
            'token',
            'access_token',
        ];
        $this->getModelReflectionClass()->each(/**
         * @throws \Doctrine\DBAL\Exception
         */ function (\ReflectionClass $modelReflection) use ($columnsShouldHidden) {
            $model = $modelReflection->getName();
            /**
             * @var Model $instance
             */
            $instance = new $model();
            $columns = Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($instance->getTable());
            $columnsNeedHidden = [];

            foreach ($columns as $column) {
                if (!in_array($column->getName(), $columnsShouldHidden)) {
                    continue;
                }

                if (in_array($column->getName(), $instance->getHidden())) {
                    continue;
                }

                $columnsNeedHidden[] = $column->getName();
            }

            static::assertEmpty(
                $columnsNeedHidden,
                $this->echo($model, "\$hidden missing", $columnsNeedHidden)
            );
        });
    }
}
