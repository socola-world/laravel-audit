<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Illuminate\Support\Facades\DB;

class AuditDatabase extends Audit1
{
    public static function tables()
    {
        return once(function () {
            return DB::connection()->getDoctrineSchemaManager()->listTableNames();
        });
    }

    public static function isTableExist($table): bool
    {
        return in_array($table, self::tables());
    }
}
