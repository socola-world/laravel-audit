<?php

namespace SocolaDaiCa\LaravelAudit\Tests\App;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class ModelTest extends TestCase
{
    /**
     * @dataProvider modelDataProvider
     *
     * @throws \JsonException
     */
    public function testModelTableNotExistInDatabase(\ReflectionClass $modelReflection, Model $model, array $columns)
    {
        static::assertTrue(
            in_array(
                $model->getTable(),
                $this->getDatabaseTables(),
                true
            ),
            $this->echo(
                $modelReflection->name,
                'table',
                $model->getTable(),
                'not exist in database'
            )
        );
        $this->followTestColumnOfFillableNotExistInDatabase($modelReflection, $model, $columns);
        $this->followTestColumnOfGuaredNotExistInDatabase($modelReflection, $model, $columns);
        $this->followTestFillableOrGuardedMissing($modelReflection, $model, $columns);
        $this->followTestPrimaryKey($modelReflection, $model, $columns);
        $this->followTestRelations($modelReflection, $model, $columns);
    }

    public function followTestColumnOfFillableNotExistInDatabase(
        \ReflectionClass $modelReflection,
        Model $model,
        array $columns
    ) {
        $columnsNotExist = array_diff($model->getFillable(), array_keys($columns));
        static::assertEmpty(
            $columnsNotExist,
            $this->echo(
                $modelReflection->getName(),
                'column of $fillable not exist in database',
                $columnsNotExist
            )
        );
    }

    public function followTestColumnOfGuaredNotExistInDatabase(
        \ReflectionClass $modelReflection,
        Model $model,
        array $columns
    ): void {
        $columnsNotExist = array_diff($model->getGuarded(), [...array_keys($columns), '*']);
        static::assertEmpty(
            $columnsNotExist,
            $this->echo(
                $modelReflection->getName(),
                'column of $guarded not exist in database',
                $columnsNotExist
            )
        );
    }

    public function followTestFillableOrGuardedMissing(
        \ReflectionClass $modelReflection,
        Model $model,
        array $columns
    ) {
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

            $columnsNeedFillable[] = $column->getName();
        }

        static::assertEmpty(
            $columnsNeedFillable,
            $this->echo(
                $modelReflection->getName(),
                '$fillable missing',
                $columnsNeedFillable
            )
        );

        static::assertEmpty(
            $columnsNeedGuarded,
            $this->echo(
                $modelReflection->getName(),
                '$guarded missing',
                $columnsNeedGuarded
            )
        );
    }

    public function followTestPrimaryKey(
        \ReflectionClass $modelReflection,
        Model $model,
        array $columns
    ) {
        static::assertArrayHasKey(
            $model->getKeyName(),
            $columns,
            $this->echo(
                $modelReflection->name,
                'column',
                $model->getKeyName(),
                'not exist in database'
            )
        );

        /**
         * @var Column $column
         */
        $column = $columns[$model->getKeyName()];

        static::assertTrue(
            $column->getNotnull(),
            $this->echo(
                $modelReflection->name,
                $column->getName(),
                'must not null'
            )
        );
    }

    protected $columnsShouldHidden = [
        'password',
        'current_password',
        'password_confirmation',
        'token',
        'access_token',
        'remember_token',
        'verification_token',
    ];

    public function follow_test_hidden(\ReflectionClass $modelReflection, Model $model, array $columns)
    {
        $hiddenMissingColumns = array_keys($columns);
        $hiddenMissingColumns = array_diff($hiddenMissingColumns, $model->getHidden());
        $hiddenMissingColumns = array_intersect($hiddenMissingColumns, $this->columnsShouldHidden);
        $hiddenMissingColumns = array_values($hiddenMissingColumns);

        $this->assertEmpty(
            $hiddenMissingColumns,
            $this->echo(
                $modelReflection->name,
                'mising hidden',
                $hiddenMissingColumns,
            )
        );
    }

    public function follow_test_soft_delete(\ReflectionClass $modelReflection, Model $model, array $columns)
    {
        $hasSoftDeletesTrail = in_array(
            'Illuminate\Database\Eloquent\SoftDeletes',
            $modelReflection->getTraitNames()
        );

        if ($hasSoftDeletesTrail) {
            $this->assertArrayHasKey(
                $model->getDeletedAtColumn(),
                $columns,
                $this->echo(
                    $modelReflection->name,
                    'SoftDeletes',
                    'missing',
                    $model->getDeletedAtColumn(),
                    'in database'
                )
            );
        }

        $hasDeletedAtColumn = array_key_exists('deleted_at', $columns);

        if ($hasDeletedAtColumn) {
            self::assertTrue(
                $hasSoftDeletesTrail,
                $this->echo(
                    $modelReflection->name,
                    'missing trail',
                    'Illuminate\Database\Eloquent\SoftDeletes'
                )
            );
        }
    }

    protected $igoreClass = [
        Pivot::class,
        Model::class,
        'Illuminate\Foundation\Auth\User',
    ];

    public function followTestRelations(
        \ReflectionClass $modelReflection,
        Model $model,
        array $columns
    ) {
        $methodRelations = [];
        $relations = collect($modelReflection->getMethods())
            ->map(function (\ReflectionMethod $method) use ($model, $modelReflection) {
                if ($method->getNumberOfParameters() > 0) {
                    return null;
                }

                if ($method->isPublic() === false) {
                    return null;
                }
                if (in_array($method->class, $this->igoreClass)) {
                    return null;
                }

                try {
                    $response = $model->{$method->getName()}();
                } catch (\Throwable $exception) {
                    return null;
                }

                if (!is_object($response) || ($response instanceof Relation) == false) {
                    return null;
                }

                return [
                    'name' => $method->getName(),
                    'type' => get_class($response),
                    'relation' => $response,
                ];
            })
            ->filter(fn($e) => $e != null);
//            ->map(fn (\ReflectionMethod $method) => [$method->getName(), get_class($model->{$method->getName()}())]);
        $relations->groupBy('type');
//        dd($relations);
    }
    ////    public function test_fillable()
////    {
////        $this->getModelReflectionClass()->each(/**
////         * @throws \Doctrine\DBAL\Exception
////         */ function (\ReflectionClass $modelReflection) {
////            $model = $modelReflection->getName();
////            /**
////             * @var Model $instance
////             */
////            $instance = new $model();
////            $columns = Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($instance->getTable());
////            $columnsNeedFillable = [];
////            $columnsNeedGuarded = [];
////
////            foreach ($columns as $column) {
////                if ($column->getAutoincrement() !== $instance->isFillable($column->getName())) {
////                    continue;
////                }
////
////                if ($column->getAutoincrement()) {
////                    $columnsNeedGuarded[] = $column->getName();
////                    continue;
////                }
////
////                $needFillable[] = $column->getName();
////            }
////
////            static::assertEmpty(
////                $columnsNeedFillable,
////                "{$model} \$fillable missing ".
////                json_encode($columnsNeedFillable, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
////            );
////
////            static::assertEmpty(
////                $columnsNeedGuarded,
////                "{$model} \$guarded missing ".
////                json_encode($columnsNeedGuarded, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
////            );
////        });
////    }
//
//    public function test_hidden()
//    {
//        $columnsShouldHidden = [
//            'password',
//            'current_password',
//            'password',
//            'password_confirmation',
//            'token',
//            'access_token',
//        ];
//        $this->getModelReflectionClass()->each(/**
//         * @throws \Doctrine\DBAL\Exception
//         */ function (\ReflectionClass $modelReflection) use ($columnsShouldHidden) {
//            $model = $modelReflection->getName();
//            /**
//             * @var Model $instance
//             */
//            $instance = new $model();
//            $columns = Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($instance->getTable());
//            $columnsNeedHidden = [];
//
//            foreach ($columns as $column) {
//                if (!in_array($column->getName(), $columnsShouldHidden)) {
//                    continue;
//                }
//
//                if (in_array($column->getName(), $instance->getHidden())) {
//                    continue;
//                }
//
//                $columnsNeedHidden[] = $column->getName();
//            }
//
//            static::assertEmpty(
//                $columnsNeedHidden,
//                $this->echo($model, "\$hidden missing", $columnsNeedHidden)
//            );
//        });
//    }
}
