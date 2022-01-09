<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Illuminate\Routing\Route;

class AuditRoute
{
    /**
     * @var Route
     */
    public $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public static function make(Route $route)
    {
        return new static($route);
    }
}
