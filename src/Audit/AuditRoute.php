<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Invades\Invade;
use SocolaDaiCa\LaravelAudit\Invades\InvadeRoute;

class AuditRoute
{
    /**
     * @var InvadeRoute|Route
     */
    public $route;

    public function __construct(Route $route)
    {
        $this->route = Invade::make($route);
    }

    public static function make(Route $route)
    {
        return new static($route);
    }

    /**
     * Get the controller method used for the route.
     *
     * @return string
     */
    public function getControllerMethod()
    {
        return $this->parseControllerCallback()[1];
    }

    /**
     * Get the controller class used for the route.
     *
     * @return string
     */
    public function getControllerClass()
    {
        return $this->parseControllerCallback()[0];
    }

    /**
     * Parse the controller.
     *
     * @return array
     */
    protected function parseControllerCallback()
    {
        return Str::parseCallback($this->route->action['uses']);
    }
}
