<?php

namespace SocolaDaiCa\LaravelAudit\TestCases;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use SocolaDaiCa\LaravelAudit\Audit\AuditClass;
use SocolaDaiCa\LaravelAudit\Audit\AuditRoute;
use SocolaDaiCa\LaravelAudit\Audit\LocalAudit;
use Symfony\Component\Finder\SplFileInfo;
use function collect;
use function once;

class RoutesTest extends TestCase
{
    public function testDuplicateMiddleware()
    {
        $duplicateMiddlewares = [];

        foreach ($this->routeDataProvider() as $routeProvider) {
            /**
             * @var AuditRoute $auditRoute
             */
            $auditRoute = $routeProvider[0];

            $middlewares = $auditRoute->route->getAction('middleware');

            $duplicateMiddleware = $middlewares;
            $duplicateMiddleware = array_count_values($duplicateMiddleware);
            $duplicateMiddleware = array_filter($duplicateMiddleware, fn ($e) => $e > 1);
            $duplicateMiddleware = array_keys($duplicateMiddleware);

            if (empty($duplicateMiddleware)) {
                continue;
            }

            $duplicateMiddlewares[] = [
                'url' => $auditRoute->route->uri(),
                'controller' => $auditRoute->route->getController(),
                'middleware' => $middlewares,
            ];
        }

        static::assertEmpty(
            $duplicateMiddlewares,
            $this->error(
                'duplicate middleware',
                $duplicateMiddlewares,
            ),
        );
    }

    public function testDuplicateActionName()
    {
        $duplicateActionNames = [];

        foreach ($this->routeDataProvider() as $routeProvider) {
            /**
             * @var AuditRoute $auditRoute
             */
            $auditRoute = $routeProvider[0];

            $actionName = $auditRoute->route->getActionName();

            $duplicateActionNames[] = $actionName;
        }

        $duplicateActionNames = array_count_values($duplicateActionNames);
        $duplicateActionNames = array_filter($duplicateActionNames, fn($count) => $count > 1);
        $duplicateActionNames = array_keys($duplicateActionNames);

        static::assertEmpty(
            $duplicateActionNames,
            $this->error(
                'duplicate action name',
                $duplicateActionNames,
            ),
        );
    }

    public function testFilePath()
    {
        $fileWrongPaths = collect(File::allFiles(base_path('routes')))
            ->map(fn (SplFileInfo $file) => $file->getRelativePathname())
            ->filter(fn ($relativePathName) => Str::contains($relativePathName, ['/', '\\']))
            ->values()
            ->toArray()
        ;

        $fileWrongPaths = array_flip($fileWrongPaths);
        $fileWrongPathsReals = [];

        foreach ($fileWrongPaths as $fromPath => $index) {
            $fromPath = str_replace('\\', '/', $fromPath);
            $toPath = str_replace('/', '_', $fromPath);

            $fromPath = 'routes/'.$fromPath;
            $toPath = 'routes/'.$toPath;

            $fileWrongPathsReals[$fromPath] = $toPath;
        }

        $fileWrongPaths = $fileWrongPathsReals;

        $this->assertEmpty(
            $fileWrongPaths,
            $this->error(
                'dont put file route in nested folder',
                $fileWrongPaths
            )
        );
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testUrl(AuditRoute $auditRoute)
    {
        $method = array_intersect(['POST', 'PUT', 'PATCH'], $auditRoute->route->methods());
        $method = $method[0] ?? null;

        preg_match('/(?<postfix>\/create|store|edit|update|delete\/?)$/', $auditRoute->route->uri(), $matches);
        $postfix = $matches['postfix'] ?? null;

        static::assertFalse(
            $method && $postfix,
            $this->error(
                "remove postfix \"{$postfix}\" on route({$auditRoute->route->getName()})",
                "{$method} {$auditRoute->route->uri()}",
            ),
        );
    }

    public function testRouteName()
    {
        $routesWrongName = collect($this->routeDataProvider())
            ->map(fn ($e) => $e[0])
            ->map(fn (AuditRoute $auditRoute) => $auditRoute->route->getName())
            ->filter(fn ($routeName) => !empty($routeName))
            ->filter(fn ($routeName) => preg_match('/^[a-z0-9\-\/\\\.]+$/', $routeName) == false)
            ->values()
            ->toArray()
        ;

        static::assertEmpty(
            $routesWrongName,
            $this->error(
                'route name should is kebab-case',
                $routesWrongName,
            ),
        );
    }

    public function routeHandleControllerDataProvider(): array
    {
        return once(function () {
            return collect($this->routeDataProvider())
                ->filter(function ($e) {
                    /**
                     * @var AuditRoute $auditRoute
                     */
                    $auditRoute = $e[0];

                    if ($auditRoute->route->action['uses'] instanceof Closure) {
                        return false;
                    }

                    return !(LocalAudit::isClassExist($auditRoute->getControllerClass()) == false);
                })
                ->map(function ($e) {
                    /* @var AuditRoute $auditRoute */
                    $auditRoute = $e[0];
                    $auditClass = AuditClass::makeByClass($e[0]->getControllerClass());

                    return [
                        $e[0],
                        $auditClass,
                        $auditClass->reflectionClass->getMethod($auditRoute->getControllerMethod()),
                    ];
                })
                ->values()
                ->toArray()
            ;
        });
    }

    /**
     * @dataProvider routeHandleControllerDataProvider
     *
     * @throws ReflectionException
     */
    public function testHandleControllerRequest(AuditRoute $auditRoute, AuditClass $auditClass, ReflectionMethod $method)
    {
        $parameterTypes = collect($method->getParameters())
            ->filter(function (ReflectionParameter $parameter) {
                return $parameter->getType() != null && $parameter->getType()->isBuiltin() == false;
            })
            ->map(fn (ReflectionParameter $parameter) => $parameter->getType()->getName())
            ->values()
        ;

        static::assertEmpty(
            $parameterTypes->filter(fn ($type) => $type == Request::class)->count(),
            $this->error(
                "{$auditRoute->route->action['uses']}",
                "\nuse php artisan make:request instead",
                Request::class,
            ),
        );
    }

    /**
     * @dataProvider routeDataProvider
     *
     * @throws ReflectionException
     */
    public function testParameters(AuditRoute $auditRoute)
    {
        $this->shouldWarning(fn () => static::assertLessThanOrEqual(
            1,
            count($auditRoute->route->parameterNames()),
            $this->error(
                $auditRoute->route->methods(),
                $auditRoute->route->uri(),
                $auditRoute->route->getName(),
                "\nToo many parameters",
                $auditRoute->route->parameterNames(),
                "\nparameters should less than or equal 1",
            ),
        ));
    }
}
