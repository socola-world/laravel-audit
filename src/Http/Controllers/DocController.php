<?php

namespace SocolaDaiCa\LaravelAudit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Audit\AuditRequest;
use SocolaDaiCa\LaravelAudit\Audit\LocalAudit;
use SocolaDaiCa\LaravelAudit\ValidatorX;

class DocController extends Controller
{
    public function index()
    {
        $ignorePaths = [
        ];

        $ignoreClass = [
            'Barryvdh\\Debugbar',
            'Facade\\Ignition',
            'SocolaDaiCa\\LaravelAudit',
            'Illuminate\\Broadcasting',
            '\\Illuminate\\Broadcasting',
        ];

        $routes = Route::getRoutes()->getRoutes();

        $routes = collect($routes)
            ->filter(function (\Illuminate\Routing\Route $item) use ($ignoreClass) {
                return Str::startsWith($item->getActionName(), $ignoreClass) === false;
            })
            ->values();

        $items = [];

        /**
         * @var \Illuminate\Routing\Route $route
         */
        foreach ($routes as $route) {
            $item = [];
            $item['request'] = 'Request';
            $item['action'] = $route->getActionName();
            $item['controller'] = Str::before($route->getActionName(), '@');
            $item['method'] = $route->getActionMethod();
            $item['name'] = $route->getName();
            $item['methods'] = array_values(array_filter($route->methods(), fn ($e) => $e !== 'HEAD'));
            $item['middlewares'] = $route->getAction('middleware');
            $item['url'] = '/'.trim($route->uri(), '/');

            if ($route->getDomain()) {
                $item['url'] = ($route->secure() ? 'https://' : 'http://').$route->getDomain().$item['url'];
            }

            $item['rules'] = [];

            if ($item['controller'] == 'Closure') {
                $method = new \ReflectionFunction($route->action['uses']);
            } else {
                $method = new \ReflectionMethod($item['controller'], $item['method']);
            }

            if (!empty($method->getParameters())) {
                $request = null;
                /**
                 * @var ReflectionParameter $parameter
                 */
                foreach ($method->getParameters() as $parameter) {
                    if (!$parameter->hasType()) {
                        continue;
                    }

                    if (!class_exists($parameter->getType()->getName())) {
                        continue;
                    }

                    $x = new \ReflectionClass($parameter->getType()->getName());

                    if ($x->isSubclassOf(FormRequest::class) === false) {
                        continue;
                    }

                    if (LocalAudit::isClassExist($x->getName()) == false) {
                        continue;
                    }

                    $auditRequest = AuditRequest::makeByClass($x->getName());
                    $request = $auditRequest->getRequest();
                    $item['request'] = $x->getName();

                    break;
                }

                if ($request) {
                    $validator = ValidatorX::make($request);
                    $item['rules'] = $validator->getRules();

                    foreach ($item['rules'] as &$rules) {
                        $rules = array_values(array_map(fn ($e) => is_object($e) ? get_class($e) : $e, $rules));
                    }

                    $item['attributes'] = $validator->customAttributes;
                    $item['messages'] = $validator->availableErrors();
                }
            }
            $items[] = $item;
        }

        $items = collect($items)->sort(function ($a, $b) {
            return strcmp($a['controller'], $b['controller']) ?: strcmp($a['method'], $b['method']);
        })->values()->toArray();

        $groupItems = collect($items)->groupBy('controller');

        return view('laravel-audit::pages.docs.index', [
            'items' => $items,
            'groupItems' => $groupItems,
        ]);
    }
}
