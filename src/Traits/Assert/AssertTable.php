<?php

namespace SocolaDaiCa\LaravelAudit\Traits\Assert;

use SocolaDaiCa\LaravelAudit\Traits\Cacheable;

trait AssertTable
{
    use Cacheable;

    public function assertUnique(string $table, array $columns)
    {
    }
}
