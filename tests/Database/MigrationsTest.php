<?php
namespace SocolaDaiCa\LaravelAudit\Tests\Database;

use Closure;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class MigrationsTest extends TestCase
{
    public function testDesign()
    {
        $function = config('socoladaica__laravel_audit.database_design');
        if (($function instanceof Closure) == false) {
            return;
        }
        Storage::drive('local')->put('laravel-audit-database.sqlite', '');

        $defaultConnection = DB::getDefaultConnection();
        DB::setDefaultConnection('laravel_audit_sqlite');
//        Artisan::call('migrate:reset', ['--force' => true]);

        \DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        $tables = \DB::select('SHOW TABLES');
        foreach($tables as $table){
            $table = implode(json_decode(json_encode($table), true));
            \Schema::drop($table);
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $function();
        DB::setDefaultConnection($defaultConnection);

//        $schema = DB::connection('laravel_audit_sqlite')->getSchemaBuilder();
        $dbDesign = DB::connection('laravel_audit_sqlite')->getDoctrineSchemaManager();
        $db = DB::connection()->getDoctrineSchemaManager();

        $dbDesignTables = $dbDesign->listTableNames();
        $dbTables = $db->listTableNames();

        $tableNotExistsInDatasbaseDesign = array_values(array_diff($dbTables, $dbDesignTables, ['migrations']));
        $this->assertEmpty(
            $tableNotExistsInDatasbaseDesign,
            $this->error(
                'database has tables but not defined in database_design',
                $tableNotExistsInDatasbaseDesign
            )
        );

        $tableNotExistsInDatasbase = array_values(array_diff($dbDesignTables, $dbTables));
        $this->assertEmpty(
            $tableNotExistsInDatasbase,
            $this->error(
                'database missing tables',
                $tableNotExistsInDatasbase
            )
        );

        foreach ($dbDesignTables as $table) {
            $dbDesignColumns = $dbDesign->listTableColumns($table);
//            if (array_key_exists('"action"', $dbDesignColumns)) {
//                $dbDesignColumns['action'] = $dbDesignColumns['"action"'];
//                unset($dbDesignColumns['"action"']);
//            }

            $dbColumns = $db->listTableColumns($table);

            $columnsNotExistsInDatasbaseDesign = array_keys(array_diff_key($dbColumns, $dbDesignColumns));
            $this->assertEmpty(
                $columnsNotExistsInDatasbaseDesign,
                $this->error(
                    "database table {$table} has columns but not defined in database_design",
                    $columnsNotExistsInDatasbaseDesign
                )
            );


            $columnsNotExistsInDatasbase = array_keys(array_diff_key($dbDesignColumns, $dbColumns));
            $this->assertEmpty(
                $columnsNotExistsInDatasbase,
                $this->error(
                    "database table {$table} missing columns",
                    $columnsNotExistsInDatasbase
                )
            );

            /**
             * @var Column $column
             */
            foreach ($dbColumns as $columnName => $column) {
                $keys = [
                    'name',
                    'type',
                    'default',
                    'notnull',
                    'length',
                    'precision',
                    'scale',
                    'fixed',
                    'unsigned',
                    'autoincrement',
                    'columnDefinition',
                    'comment',
                ];

                $a = $dbDesignColumns[$columnName]->toArray();
                $b = $column->toArray();

                foreach ($keys as $key) {
                    $this->assertEquals(
                        json_encode($a[$key]),
                        json_encode($b[$key]),
                        $this->error(
                            "{$table} $columnName {$key} should is",
                            
                        )
                    );
                }
            }
        }

//        $this->assesêm
//        $tables = ->listTableNames();

//        $columns = DB::connection('laravel_audit_sqlite')
//            ->getDoctrineSchemaManager()
//            ->listTableColumns($tables[0]);
//        dd($columns);
//        dd(Schema::connection('laravel_audit_sqlite')
//            ->getDoctrineSchemaManager()
//            ->listTableColumns();
    }
}
