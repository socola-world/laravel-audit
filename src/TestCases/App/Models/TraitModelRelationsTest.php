<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionMethod;
use SocolaDaiCa\LaravelAudit\Audit\AuditModel;
use Throwable;
use function collect;

trait TraitModelRelationsTest
{
    /**
     * @dataProvider modelExistsDataProvider
     */
    public function testRelations(AuditModel $auditModel)
    {
        $relations = collect($auditModel->reflectionClass->getMethods())
            ->map(function (ReflectionMethod $method) use ($auditModel) {
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
                } catch (Throwable $exception) {
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
            ->filter(fn ($e) => $e != null)
        ;
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
                                ),
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
            $methodName,
        );
    }

    public function followRelationHasOne(AuditModel $auditModel, HasOne $relation, ReflectionMethod $method)
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
            $methodName,
        );

        $this->shouldWarning(function () use ($auditModel, $relation, $method, $foreign) {
            $this->assertTrue(
                $auditModel->auditTable->isUnique($relation->getForeignKeyName()),
                $this->error(
                    "{$method->getDeclaringClass()->getName()}::{$method->getName()}()",
                    $foreign->model->getTable(),
                    'missing unique('.implode(', ', Arr::wrap($relation->getForeignKeyName())).')',
                ),
            );
        });
    }

    public function followRelationHasMany(HasMany $relation, ReflectionMethod $method)
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
            $methodName,
        );
    }

    public function followRelationMorphMany(MorphMany $relation, ReflectionMethod $method)
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
            $methodName,
        );
    }

    public function followRelationBelongsToMany(BelongsToMany $relation, ReflectionMethod $method)
    {
        $className = get_class($relation->getParent());
        $methodName = $method->getName();

        static::assertNotEquals(
            'Illuminate\Database\Eloquent\Relations\Pivot',
            $relation->getPivotClass(),
            $this->error(
                "{$method->getDeclaringClass()->getName()}::{$method->getName()}()",
                'argument $this->belongsToMany::$table should is class extend Illuminate\\Database\\Eloquent\\Relations\\Pivot',
            ),
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
            $methodName,
        );

        $this->followTestRelationKeys(
            $using,
            $relation->getRelatedPivotKeyName(),
            $foreign,
            $relation->getRelatedKeyName(),
            $className,
            $methodName,
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
            ),
        );

        static::assertTrue(
            $owner->isColumnExist($ownerKeyName),
            $this->error(
                "{$className}::{$methodName}()",
                'column',
                $ownerKeyName,
                'not found in table',
                $owner->model->getTable(),
            ),
        );
    }
}
