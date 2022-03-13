<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Composer\Autoload\ClassLoader;
use Composer\Autoload\ClassMapGenerator;

class Audit1
{
    protected static ClassLoader $loader;

    public static function getLoader(): ClassLoader
    {
        if (empty(self::$loader)) {
            self::$loader = require base_path('vendor\autoload.php');
        }

        return self::$loader;
    }

    protected static $classMap;

    public static function getClassMap(): array
    {
        if (!empty(self::$classMap)) {
            return self::$classMap;
        }

        $composerJson = json_decode(file_get_contents(base_path('composer.json')));

        $paths = array_merge(
            (array) data_get($composerJson, 'autoload.psr-4', []),
            (array) data_get($composerJson, 'autoload-dev.psr-4', []),
        );

        foreach ($paths as $path) {
            self::getLoader()->addClassMap((array) ClassMapGenerator::createMap($path));
        }

        return self::$classMap = self::getLoader()->getClassMap();
    }

    private static array $fileMap;

    public static function getFileMap(): array
    {
        if (empty(self::$fileMap)) {
            self::$fileMap = [];

            foreach (self::getClassMap() as $class => $path) {
                self::$fileMap[realpath($path)] = $class;
            }
        }

        return self::$fileMap;
    }

    public static function getClassByFile($path)
    {
        return self::getFileMap()[realpath($path)] ?: null;
    }

    public static function isClassExist($class): bool
    {
        return class_exists($class)
            || interface_exists($class)
            || array_key_exists($class, self::getClassMap());
    }
}
