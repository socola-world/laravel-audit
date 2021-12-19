<?php

namespace SocolaDaiCa\LaravelAudit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use SocolaDaiCa\LaravelAudit\Helper;
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
            ->values()
        ;

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
            $item['methods'] = array_values(array_filter($route->methods(), fn($e) => $e !== 'HEAD'));
            $item['middlewares'] = $route->getAction('middleware');
            $item['url'] = '/'.trim($route->uri(), '/');
            if ($route->getDomain()) {
                $item['url'] =  ($route->secure() ? 'https://' : 'http://').$route->getDomain().$item['url'];
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

                    $request = Helper::getRequest($x->getName());
                    $item['request'] = $x->getName();
                    break;
                }

//                if ($route->getActionName() == 'App\Http\Controllers\StoreManager\Tournament\EventController@update') {
//                    dd('a', $item, $request);
//                }

                if ($request) {
                    /**
                     * @var Validator $validator
                     */
                    $validator = $request->getValidator();

                    $item['rules'] = $validator->getRules();

                    foreach ($item['rules'] as &$rules) {
                        $rules = array_values(array_map(fn($e) => is_object($e) ? get_class($e) : $e, $rules));
                    }

                    $item['attributes'] = $validator->customAttributes;
//                    if ($item['controller'] == 'App\Http\Controllers\StoreManager\SeriesController') {
//                        dd($validator);
//////                        dd($validator->messages());
//////                        dd($validator->replacers);
//                    }


                    $y = ValidatorX::make($validator);
                    $item['messages'] = $y->availableErrors();

//                    if ($item['controller'] == 'App\Http\Controllers\Admin\StoreController') {
////                        dd($validator);
////                        dd($validator->messages());
//                    }
                }
            }


//            if ($route->getActionName() == 'App\Http\Controllers\StoreManager\Tournament\EventController@update') {
//                dd($item);
//                dd($validator->customAttributes);
//                dd('x');
//            }
            $items[] = $item;
        }

        return view('laravel-audit::pages.docs.index', [
            'items' => $items,
        ]);
    }
}
