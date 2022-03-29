<?php

namespace SocolaDaiCa\LaravelAudit\Traits;

use Closure;

trait Cacheable
{
    protected array $cache = [];

    public function cache(string $key, Closure $closure)
    {
        if (array_key_exists($key, $this->cache) == false) {
            $this->cache[$key] = $closure();
        }

        return $this->cache[$key];
    }

    public function clearCache($key = null)
    {
        if ($key) {
            unset($this->cache[$key]);

            return;
        }

        $this->cache = [];
    }
}
