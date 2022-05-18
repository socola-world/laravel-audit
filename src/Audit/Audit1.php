<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Composer\Autoload\ClassLoader;
use Composer\Autoload\ClassMapGenerator;

class Audit1
{
    /**
     * @return void
     */
    public function abc()
    {
    }

    protected static ClassLoader $loader;

    public static function getLoader(): ClassLoader
    {
        if (empty(static::$loader)) {
            static::$loader = require base_path('vendor\autoload.php');
        }

        return static::$loader;
    }

    protected static $classMap;

    public static function getClassMap(): array
    {
        if (!empty(static::$classMap)) {
            return static::$classMap;
        }

        $composerJson = json_decode(file_get_contents(base_path('composer.json')));

        $paths = array_merge(
            (array) data_get($composerJson, 'autoload.psr-4', []),
            (array) data_get($composerJson, 'autoload-dev.psr-4', []),
        );

        foreach ($paths as $path) {
            static::getLoader()->addClassMap((array) ClassMapGenerator::createMap($path));
        }

        return static::$classMap = static::getLoader()->getClassMap();
    }

    private static array $fileMap;

    public static function getFileMap(): array
    {
        if (empty(static::$fileMap)) {
            static::$fileMap = [];

            foreach (static::getClassMap() as $class => $path) {
                static::$fileMap[realpath($path)] = $class;
            }
        }

        return static::$fileMap;
    }

    public static function getClassByFile($path)
    {
        return static::getFileMap()[realpath($path)] ?: null;
    }

    public static function isClassExist($class): bool
    {
        return class_exists($class)
            || interface_exists($class)
            || array_key_exists($class, static::getClassMap());
    }
}
