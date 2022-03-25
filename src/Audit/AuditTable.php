<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

use Doctrine\DBAL\Schema\Index;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use SocolaDaiCa\LaravelAudit\Traits\Cacheable;

class AuditTable
{
    use Cacheable;
    protected string $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public static function make(string $table): AuditTable
    {
        return new static($table);
    }

    public function indexes(): Collection
    {
        return $this->cache('indexes', function () {
            $schema = DB::getDoctrineSchemaManager();
            $indexes = collect($schema->listTableIndexes($this->table))
                ->map(function (Index $index) {
                    return [
                        'columns' => $index->getColumns(),
                        'name' => $index->getName(),
                        'is_unique' => $index->isUnique(),
                        'is_primary' => $index->isPrimary(),
                    ];
                })
                ->toArray();
            ksort($indexes);

            return collect($indexes);
        });
    }

    /**
     * @param array|string $columns
     * @return bool
     */
    public function isUnique($columns): bool
    {
        $columns = Arr::wrap($columns);

        return $this->indexes()->contains(fn ($item) => $item['is_unique'] && $item['columns'] == $columns);
    }
}
