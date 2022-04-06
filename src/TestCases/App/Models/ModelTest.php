<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\App\Models;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Arr;
use JsonException;
use SocolaDaiCa\LaravelAudit\Audit\AuditDatabase;
use SocolaDaiCa\LaravelAudit\Audit\AuditModel;
use SocolaDaiCa\LaravelAudit\Helper;
use SocolaDaiCa\LaravelAudit\TestCases\TestCase;
use SocolaDaiCa\LaravelAudit\Traits\Assert\AssertTable;
use function collect;

class ModelTest extends TestCase
{
    use AssertTable;
    use TraitModelAppendsTest;
    use TraitModelRelationsTest;

    /**
     * @dataProvider modelDataProvider
     *
     * @throws JsonException
     */
    public function testModelTableNotExistInDatabase(AuditModel $auditModel)
    {
        static::assertTrue(
            AuditDatabase::isTableExist($auditModel->model->getTable()),
            $this->error(
                $auditModel->reflectionClass->name,
                'table',
                $auditModel->model->getTable(),
                'not exist in database',
            ),
        );
    }

    public function modelExistsDataProvider()
    {
        $models = array_filter(
            $this->modelDataProvider(),
            fn ($provider) => AuditDatabase::isTableExist($provider[0]->model->getTable()),
        );

        return array_values($models);
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
                $columnsNotExist,
            ),
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
                $columnsNotExist,
            ),
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
                $columnsNeedFillable,
            ),
        );

        static::assertEmpty(
            $columnsNeedGuarded,
            $this->error(
                $auditModel->reflectionClass->getName(),
                '$guarded missing',
                $columnsNeedGuarded,
            ),
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
            ),
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
                    'must not null',
                ),
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
            ->toArray()
        ;

        static::assertEmpty(
            $hiddenMissingColumns,
            $this->error(
                $auditModel->reflectionClass->getName(),
                'mising hidden',
                $hiddenMissingColumns,
            ),
        );
    }

    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testSoftDelete(AuditModel $auditModel)
    {
        $hasSoftDeletesTrail = in_array(
            SoftDeletes::class,
            class_uses_recursive($auditModel->reflectionClass->getName()),
        );

        if ($hasSoftDeletesTrail) {
            static::assertTrue(
                $auditModel->isColumnExist($auditModel->model->getDeletedAtColumn()),
                $this->error(
                    $auditModel->reflectionClass->getName(),
                    'use SoftDeletes',
                    'but column',
                    $auditModel->model->getDeletedAtColumn(),
                    'not found',
                ),
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
                        'Illuminate\Database\Eloquent\SoftDeletes',
                    ),
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
     *
     * @throws JsonException
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
            ->toArray()
        ;

        static::assertEmpty(
            $wrongAppends,
            $this->error(
                '$appends missing get{Attribute}Attribute method',
                $wrongAppends,
            ),
        );
    }

    /**
     * @dataProvider modelDataProvider
     *
     * @throws JsonException
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
                $columnsShouldNotNull,
            ),
        );
    }

    /**
     * @dataProvider modelDataProvider
     *
     * @throws JsonException
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
                    $columnsShouldUnsigned,
                ),
            );
        });
    }

    /**
     * @dataProvider modelDataProvider
     *
     * @throws JsonException
     */
    public function testColumnName(AuditModel $auditModel)
    {
        $columnsWrongFormat = collect(array_keys($auditModel->columns))
            ->filter(fn ($columnName) => !Helper::isSnakeCase($columnName))
            ->values()
            ->toArray()
        ;

        static::assertEmpty(
            $columnsWrongFormat,
            $this->error(
                "{$auditModel->reflectionClass->getName()}::\$columns should be snake_case",
                $columnsWrongFormat,
            ),
        );
    }
}
