<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuditModel extends AuditClass
{
    /**
     * @var Model $model
     */
    public $model;
    /**
     * @var array $columns
     */
    public $columns;
    /**
     * @var array $relations
     */
    public $relations;

    /**
     * @throws \ReflectionException
     * @throws \Doctrine\DBAL\Exception
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
}
