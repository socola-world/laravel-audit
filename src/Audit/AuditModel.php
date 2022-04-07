<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use Throwable;

class AuditModel extends AuditClass
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var array
     */
    public $columns;

    /**
     * @var array
     */
    public $relations;

    public AuditTable $auditTable;

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws ReflectionException
     */
    public function __construct(ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;

        $className = $reflectionClass->getName();
        $this->model = new $className();

        $this->columns = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableColumns($this->model->getTable())
        ;

        $this->auditTable = AuditTable::make($this->model->getTable());
    }

    /**
     * @param array|string $columns
     */
    public function isColumnExist($columns): bool
    {
        $columns = Arr::wrap($columns);

        return count(array_diff($columns, array_keys($this->columns))) == 0;
    }

    /**
     * @throws ReflectionException
     */
    public function isRelation($relation)
    {
        if ($this->reflectionClass->hasMethod($relation) === false) {
            return false;
        }

        $method = $this->reflectionClass->getMethod($relation);

        if ($method->getNumberOfParameters() > 0) {
            return false;
        }

        if ($method->isPublic() === false) {
            return false;
        }

        if (in_array($method->class, $this->igoreClass)) {
            return false;
        }

        if (Str::start($method->getName(), 'get') && Str::endsWith($method->getName(), 'Attribute')) {
            return false;
        }

        try {
            $response = $this->model->{$method->getName()}();
        } catch (Throwable $exception) {
            return false;
        }

        return !(!is_object($response) || ($response instanceof Relation) === false)
         ;
    }

    public function isColumnVisible($column)
    {
        return in_array($column, $this->model->getVisible()) || !in_array($column, $this->model->getHidden());
    }

    /**
     * @throws ReflectionException
     */
    public function getAppends()
    {
        $getArrayableAppends = $this->reflectionClass->getMethod('getArrayableAppends');
        $getArrayableAppends->setAccessible(true);

        return $getArrayableAppends->invokeArgs($this->model, []);
    }

    protected array $columnsShouldNotNull = [
        'fee',
        'quantity',
        'amount',
        'amounts',
        'number',
        'total',
    ];

    public function isColumnShouldNotNull(string $column): bool
    {
        foreach ($this->columnsShouldNotNull as $columnShouldNotNull) {
            if (
                $this->columns[$column]->getName() == $columnShouldNotNull
                || Str::startsWith($this->columns[$column]->getName(), "{$columnShouldNotNull}_")
                || Str::endsWith($this->columns[$column]->getName(), "_{$columnShouldNotNull}")
                || Str::contains($this->columns[$column]->getName(), "{$columnShouldNotNull}")
            ) {
                return true;
            }
        }

        return false;
    }

    protected array $columnsShouldUnsigned = [
        'fee',
        'quantity',
        'amount',
        'amounts',
        'number',
        'total',
    ];

    public function isColumnShouldUnsigned(string $column): bool
    {
        foreach ($this->columnsShouldUnsigned as $columnsShouldUnsined) {
            if (
                $this->columns[$column]->getName() == $columnsShouldUnsined
                || Str::startsWith($this->columns[$column]->getName(), "{$columnsShouldUnsined}_")
                || Str::endsWith($this->columns[$column]->getName(), "_{$columnsShouldUnsined}")
                || Str::contains($this->columns[$column]->getName(), "{$columnsShouldUnsined}")
            ) {
                return true;
            }
        }

        return false;
    }
}
