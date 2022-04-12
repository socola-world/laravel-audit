<?php

namespace SocolaDaiCa\LaravelAudit\TestCases;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use SocolaDaiCa\LaravelAudit\Audit\AuditClass;
use SocolaDaiCa\LaravelAudit\Audit\AuditModel;
use SocolaDaiCa\LaravelAudit\Audit\AuditRequest;
use SocolaDaiCa\LaravelAudit\Audit\AuditRoute;
use SocolaDaiCa\LaravelAudit\Audit\AuditView;
use Symfony\Component\Finder\SplFileInfo;
use function once;

trait DataProvider
{
    public function classDataProvider()
    {
        return once(function () {
            $this->refreshApplication();

            return $this->getReflectionClass()->map(function (ReflectionClass $reflectionClass) {
                return [AuditClass::make($reflectionClass)];
            })->toArray();
        });
    }

    public function modelDataProvider()
    {
        return once(function () {
            $this->refreshApplication();

            return $this->getModelReflectionClass()->map(function (ReflectionClass $modelReflectionClass) {
                /**
                 * @var Model $model
                 */
                $model = $modelReflectionClass->getName();
                $model = new $model();

                return [AuditModel::make($modelReflectionClass)];
            })->toArray();
        });
    }

    public function pivotDataProvider()
    {
        return once(function () {
            return collect($this->modelDataProvider())
                ->filter(function ($item) {
                    /* @var AuditModel $auditMode */
                    $auditMode = $item[0];

                    return $auditMode->reflectionClass->isSubclassOf(Pivot::class);
                })
                ->toArray()
            ;
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

    public function routeDataProvider(): array
    {
        return once(function () {
            return collect(Route::getRoutes()->getRoutes())
                ->map(fn (\Illuminate\Routing\Route $route) => [AuditRoute::make($route)])
                ->toArray()
            ;
        });
    }

    public function requestDataProvider()
    {
        return once(function () {
            $this->refreshApplication();

            return $this->getReflectionClass()
                ->filter(function (ReflectionClass $item) {
                    return $item->isSubclassOf(FormRequest::class);
                })
                ->map(function (ReflectionClass $requestReflectionClass) {
                    $auditRequest = null;
                    /*
                     * @type \Illuminate\Foundation\Http\FormRequest $request
                     */
                    try {
                        $auditRequest = AuditRequest::make($requestReflectionClass);
                        $auditRequest->getRequest();
                    } catch (Exception $exception) {
                        return [null];
//                        dd($exception);
                    }

                    return [
                        $auditRequest,
                    ];
                })
                ->filter(function ($item) {
                    return $item[0] !== null;
                })
                ->toArray()
            ;
        });
    }

    /**
     * @return Collection|SplFileInfo[]
     */
    public function views(): Collection
    {
        return once(function () {
            $dir = resource_path('views');

            if (is_dir($dir) === false) {
                return collect([]);
            }

            /**
             * @var SplFileInfo[] $files
             */
            $files = File::allFiles($dir);

            return collect($files);
        });
    }

    public function viewDataProvider(): array
    {
        return $this->views()
            ->map(fn ($file) => [AuditView::make($file)])
            ->toArray()
        ;
    }
}
