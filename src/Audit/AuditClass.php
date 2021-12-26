<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

class AuditClass extends Audit1
{
    /**
     * @var \ReflectionClass $reflectionClass
     */
    public $reflectionClass;

    private static $cache = [];

    public static function make(\ReflectionClass $reflectionClass)
    {
        if (array_key_exists($reflectionClass->getName(), static::$cache)) {
            return static::$cache[$reflectionClass->getName()];
        }

        return static::$cache[$reflectionClass->getName()] = new static($reflectionClass);
    }

    public static function makeByClass($class)
    {
        if (array_key_exists($class, static::$cache)) {
            return static::$cache[$class];
        }

        return static::$cache[$class] = new static(new \ReflectionClass($class));
    }

    public function hasTrait($trait)
    {
        return in_array($trait, $this->reflectionClass->getTraitNames());
    }
}
