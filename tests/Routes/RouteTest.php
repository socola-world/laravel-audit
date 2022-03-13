<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Routes;

use Illuminate\Http\Request;
use ReflectionParameter;
use SocolaDaiCa\LaravelAudit\Audit\AuditClass;
use SocolaDaiCa\LaravelAudit\Audit\AuditRoute;
use SocolaDaiCa\LaravelAudit\Audit\LocalAudit;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class RouteTest extends TestCase
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
                $duplicateMiddlewares
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

        static::assertEmpty(
            $method && $postfix,
            $this->error(
                "remove postfix \"{$postfix}\" on route({$auditRoute->route->getName()})",
                "{$method} {$auditRoute->route->uri()}"
            )
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
            ->toArray();

        static::assertEmpty(
            $routesWrongName,
            $this->error(
                'route name should is kebab-case',
                $routesWrongName
            )
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

                    if ($auditRoute->route->action['uses'] instanceof \Closure) {
                        return false;
                    }

                    if (LocalAudit::isClassExist($auditRoute->getControllerClass()) == false) {
                        return false;
                    }

                    return true;
                })
                ->values()
                ->toArray();
        });
    }

    /**
     * @dataProvider routeHandleControllerDataProvider
     *
     * @throws \ReflectionException
     */
    public function testHandleControllerParamaters(AuditRoute $auditRoute)
    {
        $auditClass = AuditClass::makeByClass($auditRoute->getControllerClass());
        $method = $auditClass->reflectionClass->getMethod($auditRoute->getControllerMethod());

        $parameterTypes = collect($method->getParameters())
            ->filter(function (ReflectionParameter $parameter) {
                return $parameter->getType() != null && $parameter->getType()->isBuiltin() == false;
            })
            ->map(fn (ReflectionParameter $parameter) => $parameter->getType()->getName())
            ->values();

        static::assertEmpty(
            $parameterTypes->filter(fn ($type) => $type == Request::class)->count(),
            $this->error(
                "{$auditRoute->route->action['uses']}",
                "\nuse php artisan make:request instead",
                Request::class,
            )
        );
    }
}
