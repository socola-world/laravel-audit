<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use SocolaDaiCa\LaravelAudit\Audit\AuditModel;
use SocolaDaiCa\LaravelAudit\TestCases\TestCase;
use function dd;

class PivotTest extends TestCase
{
    /**
     * @dataProvider pivotDataProvider
     * @return void
     */
    public function testPrimaryKey(AuditModel $auditModel)
    {
        /* @var Pivot $pivot */
        $pivot = $auditModel->model;
        $auditModel->
        dd($pivot->getKey());
    }
}
