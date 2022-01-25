<?php

namespace SocolaDaiCa\LaravelAudit\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\ExpectationFailedException;
use SocolaDaiCa\LaravelAudit\Helper;
use Symfony\Component\Finder\SplFileInfo;

class TestCase extends \Tests\TestCase
{
    use DataProvider;

    public function createApplication()
    {
        $app = parent::createApplication();

        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('guard')->andReturnSelf();
        Auth::shouldReceive('id')->andReturn(1);
        Auth::shouldReceive('user')->andReturn(optional());

        return $app;
    }

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

            return Helper::getReflectionClasses();
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
                if (in_array($item->getName(), config('socoladaica.audit.ignore.class'))) {
                    return false;
                }

                return $item->isSubclassOf($parentClass);
            })->values();
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

    public function getResourceContent(SplFileInfo $splFileInfo)
    {
        return once(function () use ($splFileInfo) {
            return $splFileInfo->getContents();
        });
    }

    public function echo(string $color, array $args)
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
            $str = "{$color}{$str}\e[0m";
        }

        return $str;
    }

    /**
     * @throws \JsonException
     */
    public function error(...$args): string
    {
        return $this->echo("\e[41;97m", $args);
    }

    /**
     * @throws \JsonException
     */
    public function warning(...$args): string
    {
        return $this->echo("\e[0;33m", $args);
    }

    public function shouldWarning($fn)
    {
        try {
            $fn();
        } catch (ExpectationFailedException $exception) {
            $this->addWarning($exception->getMessage());
        }
    }
}
