<?php

namespace SocolaDaiCa\LaravelAudit\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Once\Cache;
use Symfony\Component\Finder\SplFileInfo;

class TestCase extends \Tests\TestCase
{
    /**
     * @return Collection|\ReflectionClass[]
     */
    public function getReflectionClass()
    {
        return once(function () {
            $loader = require 'vendor/autoload.php';

            $classes = $loader->getClassMap();

            $composer = json_decode(file_get_contents("composer.json"));

            $this->assertTrue(
                data_get($composer, 'config.optimize-autoloader'),
                'Please set composer.json
{
    ...
    "config": {
        "optimize-autoloader": true,
        ...
    }
    ...
}
and run "composer dumpautoload" again'
            );

            $autoloadPsr4 = array_keys(
                (array) data_get($composer, 'autoload.psr-4', [])
            );

            return collect($classes)
                ->keys()
                ->filter(function ($item) use ($autoloadPsr4) {
                    return Str::startsWith($item, $autoloadPsr4);
                })
                ->map(function ($item) {
                    return new \ReflectionClass($item);
                })
            ;
        });
    }

    /**
     * @return Collection|\ReflectionMethod[]
     */
    public function getReflectionClassMethods()
    {
        return once(function () {
            return $this->getReflectionClass()
                ->map(function (\ReflectionClass $reflectionClass) {
                    return collect($reflectionClass->getMethods())
                        ->filter(function (\ReflectionMethod $reflectionMethod) use (&$reflectionClass) {
                            return $reflectionMethod->class === $reflectionClass->getName();
                        });
                })
                ->flatten(1)
            ;
        });
    }

    /**
     * @param $parentClass
     * @return Collection|\ReflectionClass[]
     */
    public function getReflectionClassByParent($parentClass)
    {
        return once(function () use ($parentClass) {
            return $this->getReflectionClass()->filter(function (\ReflectionClass $item) use ($parentClass) {
                return $item->isSubclassOf($parentClass);
            })->values();
        });
    }

    public function modelDataProvider()
    {
        return once(function () {
            $this->refreshApplication();

            return $this->getModelReflectionClass()->map(function (\ReflectionClass $modelReflectionClass) {
                /**
                 * @var Model $model
                 */
                $model = $modelReflectionClass->getName();
                $model = new $model();
                $columns = Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($model->getTable());

                return [
                    $modelReflectionClass,
                    $model,
                    $columns,
                ];
            })->toArray();
        });
    }

    /**
     * @param $parentClass
     * @return Collection|\ReflectionClass[]
     */
    public function getModelReflectionClass()
    {
        return once(function () {
            return $this->getReflectionClassByParent(Model::class);
        });
    }

    /**
     * @param $parentClass
     * @return Collection|\ReflectionClass[]
     */
    public function getControllerReflectionClass()
    {
        return once(function () {
            return $this->getReflectionClassByParent(Controller::class);
        });
    }

    public function getClassByContains($contains)
    {
        return $this->getReflectionClass()->filter(function ($class) use ($contains) {
            return Str::contains($class, $contains);
        })->values();
    }

    public function getClassRequests()
    {
        return $this->getClassByContains('\\Requests\\');
    }

    /**
     * @param $parentClass
     * @return Collection|\ReflectionClassConstant[]
     */
    public function getReflectionConstants()
    {
        return once(function () {
            return $this->getReflectionClass()->map(function (\ReflectionClass $item) {
                return $item->getReflectionConstants();
            })->flatten(1);
        });
    }

    /**
     * @return Collection|SplFileInfo[]
     */
    public function getResources()
    {
        return once(function () {
            return collect(File::allFiles('resources'));
        });
    }

    /**
     * @return Collection|SplFileInfo[]
     */
    public function resourcesStyleDataProvider()
    {
        return once(function () {
            return $this->getResources()->filter(function (SplFileInfo $splFileInfo) {
                return $splFileInfo->getExtension() === 'scss';
            })->map(function (SplFileInfo $splFileInfo) {
                return [$splFileInfo, $splFileInfo->getContents()];
            })->toArray();
        });
    }

    public function getResourceContent(SplFileInfo $splFileInfo)
    {
        return once(function () use ($splFileInfo){
            return $splFileInfo->getContents();
        });
    }

    /**
     * @throws \JsonException
     */
    public function echo(...$args): string
    {
        $str = "";
        foreach ($args as $arg) {
            if (is_string($arg)) {
                $str .= "{$arg} ";
                continue;
            }

            if (is_array($arg)) {
                $str .= json_encode($arg, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)." ";
            }
        }

        return $str;
    }
}
