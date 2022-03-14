<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Composer\Autoload\ClassLoader;
use Composer\Autoload\ClassMapGenerator;

class LocalAudit
{
    protected static ClassLoader $loader;

    public static function getLoader(): ClassLoader
    {
        if (empty(static::$loader)) {
            static::$loader = new ClassLoader();
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
            static::getLoader()->addClassMap((array) ClassMapGenerator::createMap(base_path($path)));
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
        return array_key_exists($class, static::getClassMap());
    }
}
