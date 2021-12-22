<?php

namespace SocolaDaiCa\LaravelAudit\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use SocolaDaiCa\LaravelAudit\Helper;
use Symfony\Component\Finder\SplFileInfo;

class TestCase extends \Tests\TestCase
{
    /**
     * @return Collection|\ReflectionClass[]
     */
    public function getReflectionClass()
    {
        return once(function () {
            $composer = json_decode(file_get_contents('composer.json'));

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

            return Helper::getReflectionClass();
        });
    }

    /**
     * @param $parentClass
     *
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
     *
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
     *
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
     *
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
        return once(function () use ($splFileInfo) {
            return $splFileInfo->getContents();
        });
    }

    /**
     * @throws \JsonException
     */
    public function echo(...$args): string
    {
        $str = '';
        foreach ($args as $arg) {
            if (is_string($arg)) {
                $str .= "{$arg} ";
                continue;
            }

            if (is_array($arg)) {
                $str .= json_encode($arg, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).' ';
            }
        }

        if ($str) {
            $str = "<!--\n\n{$str}\n\n-->";
        }

        return $str;
    }

    /* dataProvider */

    public function requestDataProvider()
    {
        return once(function () {
            $this->refreshApplication();

            return $this->getReflectionClass()
                ->filter(function (\ReflectionClass $item) {
                    return $item->isSubclassOf(FormRequest::class);
                })
                ->map(function (\ReflectionClass $requestReflectionClass) {
                    $requestClassName = trim($requestReflectionClass->getName(), '\\');
                    $className = str_replace('\\', '__', $requestClassName);

                    $class = sprintf('class %s extends %s {
                        protected function failedValidation($validator)
                        {

                        }

                        public function authorize()
                        {
                            return true;
                        }

                        public function getValidator()
                        {
                            return $this->getValidatorInstance();
                        }
                    }', $className, $requestClassName);

                    if (class_exists($className) === false) {
                        eval($class);
                    }

                    /*
                     * @type \Illuminate\Foundation\Http\FormRequest $request
                     */
//                    $request = new $className();
                    try {
                        $request = app($className);
                        /**
                         * @var Validator $validator
                         */
                        $validator = $request->getValidator();
                    } catch (\Exception $exception) {
                        $this->echo('Cant Create Request instance', $requestReflectionClass->getName());
                        $request = null;
                        $validator = null;
                    }

                    return [
                        $requestReflectionClass,
                        $request,
                        $validator,
                    ];
                })
                ->filter(function ($item) {
                    return $item[1] !== null;
                })
                ->toArray();
        });
    }

    public static function getDatabaseTables()
    {
        return once(function () {
            return DB::connection()->getDoctrineSchemaManager()->listTableNames();
        });
    }
}
