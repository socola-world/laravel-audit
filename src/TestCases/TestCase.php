<?php

namespace SocolaDaiCa\LaravelAudit\TestCases;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionClass;
use ReflectionClassConstant;
use SocolaDaiCa\LaravelAudit\Helper;
use SocolaDaiCa\LaravelAudit\Migrator;
use Symfony\Component\Finder\SplFileInfo;
use function once;
use function optional;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use DataProvider;

    protected function setUp(): void
    {
        parent::setUp();

        if (
            array_key_exists(static::class, \config('socoladaica__laravel_audit.skip_testcase'))
            && in_array($this->getName(false), \config('socoladaica__laravel_audit.skip_testcase')[static::class])
        ) {
            static::markTestSkipped('socoladaica__laravel_audit.skip_testcase');
        }
    }

    public function createApplication()
    {
        /**
         * @var \Illuminate\Foundation\Application $app
         */
        $app = require 'bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app->loadEnvironmentFrom('.env');

        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('guard')->andReturnSelf();
        Auth::shouldReceive('id')->andReturn(1);
        Auth::shouldReceive('user')->andReturn(optional());

//        $app->singleton('migrator', function ($app) {
//            $repository = $app['migration.repository'];
//
//            return new Migrator($repository, $app['db'], $app['files'], $app['events']);
//        });

//        $app['config']->set('database.connections.laravel_audit_sqlite', [
//            'driver' => 'sqlite',
//            'url' => null,
//            'database' => Storage::drive('local')->path('laravel-audit-database.sqlite'),
//            'prefix' => '',
//            'foreign_key_constraints' => true,
//        ]);
        $app['config']->set('database.connections.laravel_audit_sqlite', [
            'driver' => 'mysql',
            'url' => null,
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'socoladaica__larave_audit',
            'username' => 'root',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => [],
        ]);

//        $app->singleton('migrator', function ($app) {
//            $repository = $app['migration.repository'];
//
//            return new Migrator($repository, $app['db'], $app['files'], $app['events']);
//        });

        $app->singleton(Migrator::class, function ($app) {
            $repository = $app['migration.repository'];

            return new Migrator($repository, $app['db'], $app['files'], $app['events']);
        });

        return $app;
    }

    /**
     * @return Collection|ReflectionClass[]
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
and run "composer dumpautoload" again',
            );

            return Helper::getReflectionClasses();
        });
    }

    /**
     * @param $parentClass
     *
     * @return Collection|ReflectionClass[]
     */
    public function getReflectionClassByParent($parentClass)
    {
        return once(function () use ($parentClass) {
            return $this->getReflectionClass()->filter(function (ReflectionClass $item) use ($parentClass) {
                if (in_array($item->getName(), config('socoladaica__laravel_audit.ignore.class'))) {
                    return false;
                }

                return $item->isSubclassOf($parentClass);
            })->values();
        });
    }

    /**
     * @return Collection|ReflectionClass[]
     */
    public function getModelReflectionClass()
    {
        return once(function () {
            return $this->getReflectionClassByParent(Model::class);
        });
    }

    /**
     * @return Collection|ReflectionClass[]
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
     * @return Collection|ReflectionClassConstant[]
     */
    public function getReflectionConstants()
    {
        return once(function () {
            return $this->getReflectionClass()->map(function (ReflectionClass $item) {
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

    public function jsonEncode($arg)
    {
        return json_encode($arg, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    public function varExport($expression, $tabSize = 0)
    {
        $tab = '';

        for ($i = 0; $i < $tabSize; $i++) {
            $tab .= '    ';
        }
        $export = var_export($expression, true);
        $patterns = [
            '/array \\(/' => '[',
            '/^([ ]*)\\)(,?)$/m' => '$1]$2',
            "/=>[ ]?\n[ ]+\\[/" => '=> [',
            "/([ ]*)(\\'[^\\']+\\') => ([\\[\\'])/" => '$1$2 => $3',
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        $export = preg_replace("/\n(\\s*)/", "\n$1$1{$tab}", $export);

//        if (Arr::isAssoc($expression)) {
//            (\n\t+)
//            $export = preg_replace('/\d+ => /', '$1', $export);
//        }

//        (\n\t+)
        return preg_replace('/(\n\s*)\d+ => /', '$1', $export);
    }

    /**
     * @throws JsonException
     */
    public function echo(string $color, array $args)
    {
        $str = '';

        foreach ($args as $arg) {
            if (is_string($arg)) {
                $str .= "{$arg} ";

                continue;
            }

            if (is_array($arg)) {
                $str .= $this->varExport($arg).' ';
            }
        }

        if ($str) {
            $str = "{$color}{$str}\e[0m";
        }

        return $str;
    }

    protected static $color = '';

    /**
     * @throws JsonException
     */
    public function error(...$args): string
    {
        return $this->echo(static::$color ?: "\e[41;97m", $args);
    }

    /**
     * @throws JsonException
     */
    public function warning(...$args): string
    {
        return $this->echo(static::$color ?: "\e[0;33m", $args);
    }

    public function shouldWarning($fn)
    {
        $color = static::$color;
        static::$color = "\e[0;33m";

        try {
            $fn();
        } catch (ExpectationFailedException $exception) {
            $this->addWarning($exception->getMessage());
        }
        static::$color = $color;
    }
}
