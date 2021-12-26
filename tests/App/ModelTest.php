<?php

namespace SocolaDaiCa\LaravelAudit\Tests\App;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Audit\AuditDatabase;
use SocolaDaiCa\LaravelAudit\Audit\AuditModel;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class ModelTest extends TestCase
{
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
        $this->followTestColumnOfFillableNotExistInDatabase($auditModel);
        $this->followTestColumnOfGuaredNotExistInDatabase($auditModel);
        $this->followTestFillableOrGuardedMissing($auditModel);
        $this->followTestPrimaryKey($auditModel);
        $this->followTestRelations($auditModel);
        $this->followTestHidden($auditModel);
        $this->followTestSoftDelete($auditModel);
        $this->followTestAppends($auditModel);
    }

    public function followTestColumnOfFillableNotExistInDatabase(AuditModel $auditModel) {
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

    public function followTestColumnOfGuaredNotExistInDatabase(AuditModel $auditModel) {
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

    public function followTestFillableOrGuardedMissing(AuditModel $auditModel) {
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

    public function followTestPrimaryKey(AuditModel $auditModel) {
        static::assertTrue(
            $auditModel->isColumnExist($auditModel->model->getKeyName()),
            $this->error(
                $auditModel->reflectionClass->name,
                'column',
                $auditModel->model->getKeyName(),
                'not exist in database'
            )
        );

        /**
         * @var Column $column
         */
        $column = $auditModel->columns[$auditModel->model->getKeyName()];

        static::assertTrue(
            $column->getNotnull(),
            $this->error(
                $auditModel->reflectionClass->name,
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

    public function followTestHidden(AuditModel $auditModel)
    {
        $hiddenMissingColumns = array_keys($auditModel->columns);

        $hiddenMissingColumns = collect($hiddenMissingColumns)
            ->filter(fn ($column) => $auditModel->isColumnVisble($column))
            ->filter(fn ($column) => in_array($column, $this->columnsShouldHidden))
            ->values()
            ->toArray();

        $this->assertEmpty(
            $hiddenMissingColumns,
            $this->error(
                $auditModel->reflectionClass->getName(),
                'mising hidden',
                $hiddenMissingColumns,
            )
        );
    }

    public function followTestSoftDelete(AuditModel $auditModel)
    {
        $hasSoftDeletesTrail = in_array(
            SoftDeletes::class,
            class_uses_recursive($auditModel->reflectionClass->getName())
        );

        if ($hasSoftDeletesTrail) {
            $this->assertTrue(
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
            self::shouldWarning(function ()use (&$hasSoftDeletesTrail, &$auditModel) {
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

    public function followTestRelations(AuditModel $auditModel) {
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

                try {
                    $response = $auditModel->model->{$method->getName()}();
                } catch (\Throwable $exception) {
                    dd($auditModel->reflectionClass->getName(), $method->getName());
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
            ->filter(fn($e) => $e != null);
        $relationByTypes = $relations->groupBy('type');
        foreach ($relationByTypes as $relations) {
            foreach ($relations as $relation) {
                switch ($relation['type']) {
                    case BelongsTo::class:
                        $this->followRelationBelongTo($relation['relation']);
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasOne':
                        $this->followRelationHasOne($relation['relation'], $relation['method']);
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasMany':
                        $this->followRelationHasMany($relation['relation'], $relation['method']);
                        break;
                    case 'Illuminate\Database\Eloquent\Relations\BelongsToMany':
//                        dd($relation['method'], $relation['name']);
//                        $this->followRelationBelongsToMany($relation['relation'], $relation['method']);
                        break;
                    case 'Awobaz\Compoships\Database\Eloquent\Relations\HasMany':
//                        dd($relation['method'], $relation['name']);
//                        break;
                    case 'Illuminate\Database\Eloquent\Relations\HasOneThrough':

                    case 'Illuminate\Database\Eloquent\Relations\MorphMany':
                    case 'Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo':
                    case 'Awobaz\Compoships\Database\Eloquent\Relations\HasOne':
                        break;
                    default:
//                        $this->assertTrue(false, $this->error(
//                            'relation not handle',
//                            $relation['type']
//                        ));
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

    public function followRelationHasOne(HasOne $relation, \ReflectionMethod $method)
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

    public function followTestRelationKeys(
        AuditModel $owner,
        string $ownerKeyName,
        AuditModel $foreign,
        string $foreignKeyName,
        string $className,
        string $methodName
    ) {
        $this->assertTrue(
            $foreign->isColumnExist($foreignKeyName),
            $this->error(
                "{$className}::{$methodName}()",
                "column",
                $foreignKeyName,
                'not found in table',
                $foreign->model->getTable(),
            )
        );

        $this->assertTrue(
            $owner->isColumnExist($ownerKeyName),
            $this->error(
                "{$className}::{$methodName}()",
                "column",
                $ownerKeyName,
                'not found in table',
                $owner->model->getTable()
            )
        );
    }

    public function followTestAppends(AuditModel $auditModel)
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

        $this->assertEmpty(
            $wrongAppends,
            $this->error(
                '$appends missing get{Attribute}Attribute method',
                $wrongAppends,
            )
        );

//        dd($x);
//        $auditModel->model->hasAttributeGetMutator()
    }
}
