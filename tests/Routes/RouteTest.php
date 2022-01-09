<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Routes;

use SocolaDaiCa\LaravelAudit\Audit\AuditRoute;
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
}
