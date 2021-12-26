<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Composer\Autoload\ClassLoader;

class Audit1
{
    private static ClassLoader $loader;

    public static function getLoader(): ClassLoader
    {
        if (empty(self::$loader)) {
            self::$loader = require base_path('vendor\autoload.php');
        }

        return self::$loader;
    }

    private static $classMap;

    public static function getClassMap(): array
    {
        if (empty(self::$classMap)) {
            self::$classMap = static::getLoader()->getClassMap();
        }

        return self::$classMap;
    }

    private static $fileMap;

    public static function getFileMap(): array
    {
        if (empty(static::$fileMap)) {
            static::$fileMap = [];

            foreach (static::getClassMap() as $class => $path) {
                static::$fileMap[realpath($path)] = $class;
            }
        }

        return self::$fileMap;
    }

    public static function getClassByFile($path)
    {
        return self::getFileMap()[realpath($path)] ?: null;
    }
}
