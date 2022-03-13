<?php

namespace SocolaDaiCa\LaravelAudit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use SocolaDaiCa\LaravelAudit\Audit\Audit1;

class Helper
{
    public static function getReflectionClasses()
    {
        return once(function () {
            $composer = json_decode(file_get_contents('composer.json'));

//            $loader = require 'vendor/autoload.php';
            $loader = Audit1::getLoader();

//            $classes = $loader->getClassMap();
            $classes = Audit1::getClassMap();

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
                ->mapWithKeys(function (\ReflectionClass $item) {
                    return [$item->name => $item];
                });
        });
    }

    public static function getReflectionClass($className)
    {
        return self::getReflectionClasses()[$className];
    }

    /**
     * @return Collection|\ReflectionMethod[]
     */
    public static function getReflectionClassMethods()
    {
        return once(function () {
            return self::getReflectionClasses()
                ->map(function (\ReflectionClass $reflectionClass) {
                    return collect($reflectionClass->getMethods())
                        ->filter(function (\ReflectionMethod $reflectionMethod) use (&$reflectionClass) {
                            return $reflectionMethod->class === $reflectionClass->getName();
                        });
                })
                ->flatten(1);
        });
    }

    public static function getRequest($requestClassName)
    {
        $className = str_replace('\\', '__', $requestClassName);
        $class = sprintf('class %s extends %s {
            use SocolaDaiCa\LaravelAudit\FormRequestTrait;
        }', $className, $requestClassName);

        if (class_exists($className) === false) {
            eval($class);
        }

        try {
            /**
             * @var \Illuminate\Foundation\Http\FormRequest $request
             */
            $request = app($className);
//            /**
//             * @type Validator $validator
//             */
//            $validator = $request->getValidator();
        } catch (\Exception $exception) {
            throw $exception;
            $request = null;
        }

        return $request;
    }

    public function getRequests()
    {
        return once(function () {
            return $this->getReflectionClasses()
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
}
