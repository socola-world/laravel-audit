<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \ReflectionException
     */
    public function __construct(\ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;

        $className = $reflectionClass->getName();
        $this->model = new $className();

        $this->columns = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableColumns($this->model->getTable());
    }

    public function isColumnExist($column): bool
    {
        return array_key_exists($column, $this->columns);
    }

    /**
     * @param mixed $relation
     *
     * @throws \ReflectionException
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
        } catch (\Throwable $exception) {
            return false;
        }

        if (!is_object($response) || ($response instanceof Relation) === false) {
            return false;
        }

        return true;
    }

    public function isColumnVisble($column)
    {
        return in_array($column, $this->model->getVisible()) || !in_array($column, $this->model->getHidden());
    }

    /**
     * @throws \ReflectionException
     */
    public function getAppends()
    {
        $getArrayableAppends = $this->reflectionClass->getMethod('getArrayableAppends');
        $getArrayableAppends->setAccessible(true);

        return $getArrayableAppends->invokeArgs($this->model, []);
    }

    protected $columnsShouldNotNull = [
        'fee',
        'quantity',
        'amount',
        'amounts',
        'number',
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

    protected $columnsShouldUnsined = [
        'fee',
        'quantity',
        'amount',
        'amounts',
        'number',
    ];

    public function isColumnShouldUnsigned(string $column): bool
    {
        foreach ($this->columnsShouldUnsined as $columnsShouldUnsined) {
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
