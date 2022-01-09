<?php

namespace SocolaDaiCa\LaravelAudit\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use SocolaDaiCa\LaravelAudit\Audit\AuditClass;
use SocolaDaiCa\LaravelAudit\Audit\AuditModel;
use SocolaDaiCa\LaravelAudit\Audit\AuditRoute;
use Symfony\Component\Finder\SplFileInfo;

trait DataProvider
{
    public function classDataProvider()
    {
        return once(function () {
            $this->refreshApplication();

            return $this->getReflectionClass()->map(function (\ReflectionClass $reflectionClass) {
                return [AuditClass::make($reflectionClass)];
            })->toArray();
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

                return [AuditModel::make($modelReflectionClass)];
            })->toArray();
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

    public function routeDataProvider()
    {
        return once(function () {
            return collect(Route::getRoutes()->getRoutes())
                ->map(fn (\Illuminate\Routing\Route $route) => [AuditRoute::make($route)])
                ->toArray();
        });
    }
}
