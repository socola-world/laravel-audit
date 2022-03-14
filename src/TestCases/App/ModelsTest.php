<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\App;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Audit\AuditDatabase;
use SocolaDaiCa\LaravelAudit\Audit\AuditModel;
use SocolaDaiCa\LaravelAudit\TestCases\TestCase;
use SocolaDaiCa\LaravelAudit\Traits\Assert\AssertTable;
use function collect;

class ModelsTest extends TestCase
{
    use AssertTable;
    /**
     * @dataProvider modelDataProvider
     *
     * @throws \JsonException
     */
    public function testModelTableNotExistInDatabase(AuditModel $auditModel)
    {
        static::assertTrue(
            AuditDatabase::isTableExist($auditModel->model->getTable()),
            $this->error(
                $auditModel->reflectionClass->name,
                'table',
                $auditModel->model->getTable(),
                'not exist in database'
            )
        );
    }

    public function modelExistsDataProvider()
    {
        $models = array_filter(
            $this->modelDataProvider(),
            fn ($provider) => AuditDatabase::isTableExist($provider[0]->model->getTable())
        );

        $models = array_values($models);

        return $models;
    }

    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testColumnOfFillableNotExistInDatabase(AuditModel $auditModel)
    {
        $columnsNotExist = array_diff($auditModel->model->getFillable(), array_keys($auditModel->columns));
        static::assertEmpty(
            $columnsNotExist,
            $this->error(
                $auditModel->reflectionClass->getName(),
                'column of $fillable not exist in database',
                $columnsNotExist
            )
        );
    }

    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testColumnOfGuaredNotExistInDatabase(AuditModel $auditModel)
    {
        $columnsNotExist = array_diff($auditModel->model->getGuarded(), [...array_keys($auditModel->columns), '*']);
        static::assertEmpty(
            $columnsNotExist,
            $this->error(
                $auditModel->reflectionClass->getName(),
                'column of $guarded not exist in database',
                $columnsNotExist
            )
        );
    }

    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testFillableOrGuardedMissing(AuditModel $auditModel)
    {
        $columnsNeedFillable = [];
        $columnsNeedGuarded = [];

        foreach ($auditModel->columns as $column) {
            if ($column->getAutoincrement() !== $auditModel->model->isFillable($column->getName())) {
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
            $this->error(
                $auditModel->reflectionClass->getName(),
                '$fillable missing',
                $columnsNeedFillable
            )
        );

        static::assertEmpty(
            $columnsNeedGuarded,
            $this->error(
                $auditModel->reflectionClass->getName(),
                '$guarded missing',
                $columnsNeedGuarded
            )
        );
    }

    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testPrimaryKey(AuditModel $auditModel)
    {
        static::assertTrue(
            $auditModel->isColumnExist($auditModel->model->getKeyName()),
            $this->error(
                "{$auditModel->reflectionClass->name}::\$primaryKey = ",
                $auditModel->model->getKeyName(),
                'not exist in database',
            )
        );

        foreach (Arr::wrap($auditModel->model->getKeyName()) as $columnKey) {
            /**
             * @var Column $column
             */
            $column = $auditModel->columns[$columnKey];

            static::assertTrue(
                $column->getNotnull(),
                $this->error(
                    $auditModel->reflectionClass->name,
                    $column->getName(),
                    'must not null'
                )
            );
        }
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

    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testHidden(AuditModel $auditModel)
    {
        $hiddenMissingColumns = array_keys($auditModel->columns);

        $hiddenMissingColumns = collect($hiddenMissingColumns)
            ->filter(fn ($column) => $auditModel->isColumnVisble($column))
            ->filter(fn ($column) => in_array($column, $this->columnsShouldHidden))
            ->values()
            ->toArray();

        static::assertEmpty(
            $hiddenMissingColumns,
            $this->error(
                $auditModel->reflectionClass->getName(),
                'mising hidden',
                $hiddenMissingColumns,
            )
        );
    }

    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testSoftDelete(AuditModel $auditModel)
    {
        $hasSoftDeletesTrail = in_array(
            SoftDeletes::class,
            class_uses_recursive($auditModel->reflectionClass->getName())
        );

        if ($hasSoftDeletesTrail) {
            static::assertTrue(
                $auditModel->isColumnExist($auditModel->model->getDeletedAtColumn()),
                $this->error(
                    $auditModel->reflectionClass->getName(),
                    'use SoftDeletes',
                    'but column',
                    $auditModel->model->getDeletedAtColumn(),
                    'not found'
                )
            );
        }

        $hasDeletedAtColumn = $auditModel->isColumnExist('deleted_at');

        if ($hasDeletedAtColumn) {
            static::shouldWarning(function () use (&$hasSoftDeletesTrail, &$auditModel) {
                static::assertTrue(
                    $hasSoftDeletesTrail,
                    $this->warning(
                        $auditModel->reflectionClass->getName(),
                        'missing trait',
                        'Illuminate\Database\Eloquent\SoftDeletes'
                    )
                );
            });
        }
    }

    protected $igoreClass = [
        Pivot::class,
        Model::class,
        User::class,
    ];

    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testRelations(AuditModel $auditModel)
    {
        $relations = collect($auditModel->reflectionClass->getMethods())
            ->map(function (\ReflectionMethod $method) use ($auditModel) {
                if ($method->getNumberOfParameters() > 0) {
                    return null;
                }

                if ($method->isPublic() === false) {
                    return null;
                }

                if (in_array($method->class, $this->igoreClass)) {
                    return null;
                }

                if (Str::start($method->getName(), 'get') && Str::endsWith($method->getName(), 'Attribute')) {
                    return null;
                }

                if (in_array($auditModel::getClassByFile($method->getFileName()), [
                    'Illuminate\Database\Eloquent\SoftDeletes',
                ])) {
                    return null;
                }

                if ($method->getReturnType() != null) {
                    if ($method->getReturnType()->isBuiltin()) {
                        return null;
                    }

                    if ((
                        class_exists($method->getReturnType()->getName())
                            || interface_exists($method->getReturnType()->getName())
                    )
                        && is_subclass_of($method->getReturnType()->getName(), Relation::class) === false
                    ) {
                        return null;
                    }
                }

                try {
                    $response = $auditModel->model->{$method->getName()}();
                } catch (\Throwable $exception) {
//                    dd(
//                        [
//                            '$auditModel->reflectionClass->getName()' => $auditModel->reflectionClass->getName(),
//                            '$method->getName()' => $method->getName(),
//                            '$method->getReturnType()' => $method->getReturnType(),
//                            'class_exists($method->getReturnType()->getName())' => class_exists(
//                                $method->getReturnType()->getName()
//                            ),
//                        ],
//                        $exception
//                    );

                    return null;
                }

                if (!is_object($response) || ($response instanceof Relation) != true) {
                    return null;
                }

                return [
                    'name' => $method->getName(),
                    'method' => $method,
                    'type' => get_class($response),
                    'relation' => $response,
                ];
            })
            ->filter(fn ($e) => $e != null);
        $relationByTypes = $relations->groupBy('type');

        foreach ($relationByTypes as $relations) {
            foreach ($relations as $relation) {
                switch ($relation['type']) {
                    case BelongsTo::class:
                        $this->followRelationBelongTo($relation['relation']);

                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasOne':
                        $this->followRelationHasOne($auditModel, $relation['relation'], $relation['method']);

                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasMany':
                        $this->followRelationHasMany($relation['relation'], $relation['method']);

                        break;
                    case 'Illuminate\Database\Eloquent\Relations\MorphMany':
                        $this->followRelationMorphMany($relation['relation'], $relation['method']);

                        break;
                    case 'Illuminate\Database\Eloquent\Relations\BelongsToMany':
//                        dd($relation['method'], $relation['name']);
                        $this->followRelationBelongsToMany($relation['relation'], $relation['method']);

                        break;
//                        break;
//                        $this->followRelationHasOneThrough($relation['relation'], $relation['method']);
//                    case 'Illuminate\Database\Eloquent\Relations\HasOneThrough':
                    case 'Awobaz\Compoships\Database\Eloquent\Relations\HasMany':
//                        dd($relation['method'], $relation['name']);
//                        break;
                    case 'Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo':
                    case 'Awobaz\Compoships\Database\Eloquent\Relations\HasOne':
//                        break;
                    default:
                        static::shouldWarning(function () use (&$hasSoftDeletesTrail, &$auditModel, $relation) {
                            static::assertTrue(
                                false,
                                $this->warning(
                                    "{$auditModel->reflectionClass->getName()}::{$relation['method']->getName()}",
                                    'Comming Soon: relation not handle',
                                    $relation['type'],
                                )
                            );
                        });

                        break;
                }
            }
        }
    }

    public function followRelationBelongTo(BelongsTo $relation)
    {
        $foreign = AuditModel::makeByClass(get_class($relation->getParent()));
        $owner = AuditModel::makeByClass(get_class($relation->getRelated()));

        $className = get_class($relation->getParent());
        $methodName = $relation->getRelationName();

        $this->followTestRelationKeys(
            $owner,
            $relation->getOwnerKeyName(),
            $foreign,
            $relation->getForeignKeyName(),
            $className,
            $methodName
        );
    }

    public function followRelationHasOne(AuditModel $auditModel, HasOne $relation, \ReflectionMethod $method)
    {
        $className = get_class($relation->getParent());
        $methodName = $method->getName();

        $owner = AuditModel::makeByClass(get_class($relation->getParent()));
        $foreign = AuditModel::makeByClass(get_class($relation->getRelated()));

        $this->followTestRelationKeys(
            $owner,
            $relation->getLocalKeyName(),
            $foreign,
            $relation->getForeignKeyName(),
            $className,
            $methodName
        );

        $this->shouldWarning(function () use ($auditModel, $relation, $method, $foreign) {
            $this->assertTrue(
                $auditModel->auditTable->isUnique($relation->getForeignKeyName()),
                $this->error(
                    "{$method->getDeclaringClass()->getName()}::{$method->getName()}()",
                    $foreign->model->getTable(),
                    "missing unique(".implode(', ', Arr::wrap($relation->getForeignKeyName())).")",
                )
            );
        });
    }

    public function followRelationHasMany(HasMany $relation, \ReflectionMethod $method)
    {
        $className = get_class($relation->getParent());
        $methodName = $method->getName();

        $owner = AuditModel::makeByClass(get_class($relation->getParent()));
        $foreign = AuditModel::makeByClass(get_class($relation->getRelated()));

        $this->followTestRelationKeys(
            $owner,
            $relation->getLocalKeyName(),
            $foreign,
            $relation->getForeignKeyName(),
            $className,
            $methodName
        );
    }

    public function followRelationMorphMany(MorphMany $relation, \ReflectionMethod $method)
    {
        $className = get_class($relation->getParent());
        $methodName = $method->getName();

        $owner = AuditModel::makeByClass(get_class($relation->getParent()));
        $foreign = AuditModel::makeByClass(get_class($relation->getRelated()));

        $this->followTestRelationKeys(
            $owner,
            $relation->getLocalKeyName(),
            $foreign,
            Str::after($relation->getQualifiedForeignKeyName(), '.'),
            $className,
            $methodName
        );
    }

    public function followRelationBelongsToMany(BelongsToMany $relation, \ReflectionMethod $method)
    {
        $className = get_class($relation->getParent());
        $methodName = $method->getName();

        static::assertNotEquals(
            'Illuminate\Database\Eloquent\Relations\Pivot',
            $relation->getPivotClass(),
            $this->error(
                "{$method->getDeclaringClass()->getName()}::{$method->getName()}()",
                "argument \$this->belongsToMany::\$table should is class extend Illuminate\Database\Eloquent\Relations\Pivot",
            )
        );
        $owner = AuditModel::makeByClass(get_class($relation->getParent()));
        $using = AuditModel::makeByClass($relation->getPivotClass());
        $foreign = AuditModel::makeByClass(get_class($relation->getRelated()));

        $this->followTestRelationKeys(
            $owner,
            $relation->getParentKeyName(),
            $using,
            $relation->getForeignPivotKeyName(),
            $className,
            $methodName
        );

        $this->followTestRelationKeys(
            $using,
            $relation->getRelatedPivotKeyName(),
            $foreign,
            $relation->getRelatedKeyName(),
            $className,
            $methodName
        );
    }

    public function followTestRelationKeys(
        AuditModel $owner,
        string $ownerKeyName,
        AuditModel $foreign,
        string $foreignKeyName,
        string $className,
        string $methodName
    ) {
        static::assertTrue(
            $foreign->isColumnExist($foreignKeyName),
            $this->error(
                "{$className}::{$methodName}()",
                'column',
                $foreignKeyName,
                'not found in table',
                $foreign->model->getTable(),
            )
        );

        static::assertTrue(
            $owner->isColumnExist($ownerKeyName),
            $this->error(
                "{$className}::{$methodName}()",
                'column',
                $ownerKeyName,
                'not found in table',
                $owner->model->getTable()
            )
        );
    }

    /**
     * @dataProvider modelExistsDataProvider
     *
     * @throws \JsonException
     */
    public function testAppends(AuditModel $auditModel)
    {
        $wrongAppends = collect($auditModel->getAppends())
            ->filter(function ($e) use ($auditModel) {
                if ($auditModel->model->hasGetMutator($e)) {
                    return false;
                }

                return $auditModel->model->hasAttributeGetMutator($e) === false;
            })
            ->values()
            ->toArray();

        static::assertEmpty(
            $wrongAppends,
            $this->error(
                '$appends missing get{Attribute}Attribute method',
                $wrongAppends,
            )
        );
    }

    /**
     * @dataProvider modelDataProvider
     *
     * @throws \JsonException
     */
    public function testColumnShouldNotNull(AuditModel $auditModel)
    {
        $columnsShouldNotNull = array_filter($auditModel->columns, function (Column $column) use ($auditModel) {
            return $auditModel->isColumnShouldNotNull($column->getName()) && $column->getNotnull() === false;
        });

        $columnsShouldNotNull = array_keys($columnsShouldNotNull);

        static::assertEmpty(
            $columnsShouldNotNull,
            $this->error(
                $auditModel->reflectionClass->getName(),
                '$columnsShouldNotNull = ',
                $columnsShouldNotNull
            )
        );
    }

    /**
     * @dataProvider modelDataProvider
     *
     * @throws \JsonException
     */
    public function testColumnShouldUnsigned(AuditModel $auditModel)
    {
        $columnsShouldUnsigned = array_filter($auditModel->columns, function (Column $column) use ($auditModel) {
            return $auditModel->isColumnShouldUnsigned($column->getName()) && $column->getUnsigned() == false;
        });

        $columnsShouldUnsigned = array_keys($columnsShouldUnsigned);

        $this->shouldWarning(function () use ($columnsShouldUnsigned, $auditModel) {
            static::assertEmpty(
                $columnsShouldUnsigned,
                $this->error(
                    $auditModel->reflectionClass->getName(),
                    '$columnsShouldUnsigned = ',
                    $columnsShouldUnsigned
                )
            );
        });
    }

    /**
     * @dataProvider modelDataProvider
     * @param AuditModel $auditModel
     * @return void
     * @throws \JsonException
     */
    public function testColumnName(AuditModel $auditModel)
    {
        $columnsWrongFormat = collect(array_keys($auditModel->columns))
            ->filter(fn ($columnName) => preg_match('/^[a-z0-9_]+$/', $columnName) == false)
            ->values()
            ->toArray();

        static::assertEmpty(
            $columnsWrongFormat,
            $this->error(
                '$auditModel->reflectionClass->getName()::$columns should be snake_case',
                $columnsWrongFormat
            )
        );
    }
}
