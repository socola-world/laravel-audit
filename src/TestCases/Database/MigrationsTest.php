<?php

namespace SocolaDaiCa\LaravelAudit\TestCases\Database;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SocolaDaiCa\LaravelAudit\Audit\AuditTable;
use SocolaDaiCa\LaravelAudit\Migrator;
use SocolaDaiCa\LaravelAudit\TestCases\TestCase;
use function app;
use function base_path;
use function dd;

class MigrationsTest extends TestCase
{
    public function testDesign()
    {
//        $function = config('socoladaica__laravel_audit.database_design');
//        if (($function instanceof Closure) == false) {
//            return;
//        }
//        Storage::drive('local')->put('laravel-audit-database.sqlite', '');
//
//        $defaultConnection = DB::getDefaultConnection();
//        DB::setDefaultConnection('laravel_audit_sqlite');
////        Artisan::call('migrate:reset', ['--force' => true]);
//
//        \DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
//        $tables = \DB::select('SHOW TABLES');
//        foreach ($tables as $table) {
//            $table = implode(json_decode(json_encode($table), true));
//            \Schema::drop($table);
//        }
//        \DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
//
//        $function();
//        DB::setDefaultConnection($defaultConnection);
//
////        $schema = DB::connection('laravel_audit_sqlite')->getSchemaBuilder();
//        $dbDesign = DB::connection('laravel_audit_sqlite')->getDoctrineSchemaManager();
//        $db = DB::connection()->getDoctrineSchemaManager();
//
//        $dbDesignTables = $dbDesign->listTableNames();
//        $dbTables = $db->listTableNames();
//
//        $tableNotExistsInDatasbaseDesign = array_values(array_diff($dbTables, $dbDesignTables, ['migrations']));
//        static::assertEmpty(
//            $tableNotExistsInDatasbaseDesign,
//            $this->error(
//                'database has tables but not defined in database_design',
//                $tableNotExistsInDatasbaseDesign
//            )
//        );
//
//        $tableNotExistsInDatasbase = array_values(array_diff($dbDesignTables, $dbTables));
//        static::assertEmpty(
//            $tableNotExistsInDatasbase,
//            $this->error(
//                'database missing tables',
//                $tableNotExistsInDatasbase
//            )
//        );
//
//        foreach ($dbDesignTables as $table) {
//            $dbDesignColumns = $dbDesign->listTableColumns($table);
////            if (array_key_exists('"action"', $dbDesignColumns)) {
////                $dbDesignColumns['action'] = $dbDesignColumns['"action"'];
////                unset($dbDesignColumns['"action"']);
////            }
//
//            $dbColumns = $db->listTableColumns($table);
//
//            $columnsNotExistsInDatasbaseDesign = array_keys(array_diff_key($dbColumns, $dbDesignColumns));
//            static::assertEmpty(
//                $columnsNotExistsInDatasbaseDesign,
//                $this->error(
//                    "database table {$table} has columns but not defined in database_design",
//                    $columnsNotExistsInDatasbaseDesign
//                )
//            );
//
//            $columnsNotExistsInDatasbase = array_keys(array_diff_key($dbDesignColumns, $dbColumns));
//            static::assertEmpty(
//                $columnsNotExistsInDatasbase,
//                $this->error(
//                    "database table {$table} missing columns",
//                    $columnsNotExistsInDatasbase
//                )
//            );
//
//            /**
//             * @var Column $column
//             */
//            foreach ($dbColumns as $columnName => $column) {
//                $keys = [
//                    'name',
//                    'type',
//                    'default',
//                    'notnull',
//                    'length',
//                    'precision',
//                    'scale',
//                    'fixed',
//                    'unsigned',
//                    'autoincrement',
//                    'columnDefinition',
//                    'comment',
//                ];
//
//                $a = $dbDesignColumns[$columnName]->toArray();
//                $b = $column->toArray();
//
//                foreach ($keys as $key) {
//                    static::assertEquals(
//                        json_encode($a[$key]),
//                        json_encode($b[$key]),
//                        $this->error(
//                            "{$table} ${columnName} {$key} should is",
//                        )
//                    );
//                }
//            }
//        }
//
////        $this->assesêm
////        $tables = ->listTableNames();
//
////        $columns = DB::connection('laravel_audit_sqlite')
////            ->getDoctrineSchemaManager()
////            ->listTableColumns($tables[0]);
////        dd($columns);
////        dd(Schema::connection('laravel_audit_sqlite')
////            ->getDoctrineSchemaManager()
////            ->listTableColumns();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     */
    public function testRollback()
    {
        $migrator = app('migrator');
        /**
         * @var Migrator $myMigrator
         */
        $myMigrator = app(Migrator::class);

        $migrationPaths = array_merge(
            $migrator->paths(),
            [
                app()->databasePath().DIRECTORY_SEPARATOR.'migrations',
            ],
        );

        $migrationFiles = $migrator->getMigrationFiles($migrationPaths);

        Storage::drive('local')->put('laravel-audit-database.sqlite', '');
        $defaultConnection = DB::getDefaultConnection();
        DB::setDefaultConnection('laravel_audit_sqlite');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $table = implode(json_decode(json_encode($table), true));
            Schema::drop($table);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

//        Artisan::call('migrate:reset', ['--force' => true]);
//        Artisan::call('migrate');

//        $migrationPaths = DB::table('migrations')->get()->pluck('migration');
//
//        \DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
//        $tables = \DB::select('SHOW TABLES');
//
//        foreach ($tables as $table) {
//            $table = implode(json_decode(json_encode($table), true));
//            \Schema::drop($table);
//        }
//        \DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
//
        $databaseDescribes = [];

        $migrationPaths = array_keys($migrationFiles);

        foreach ($migrationPaths as $migrationName) {
            $migrationFullPath = $migrationFiles[$migrationName];
            $migrationPath = $migrationFiles[$migrationName];
            $migrationPath = Str::replaceFirst(base_path(), '', $migrationPath);

//            DB::connection()->enableQueryLog();
//            $migrator->run([
//                $migrationPath
//            ]);
//            Artisan::call('migrate', ['--path' => $migrationPath]);
            $myMigrator->resolvePath($migrationFullPath)->up();
//            $myMigrator->runUp($migrationFullPath, 1, false);
//            $myMigrator->runMigration($myMigrator->resolvePath($migrationFullPath), 'up');

//            $query = DB::select('alter table `users` add unique `users_email_unique`(`email`)');
//            dd($query);
//            $queries = DB::getQueryLog();
//            DB::flushQueryLog();
////            $migrator->run()
//            dump($queries);
            $databaseDescribesUp = $this->getDatabaseDescribes();

//            Artisan::call('migrate:rollback', ['--step' => 1]);
            $myMigrator->resolvePath($migrationFullPath)->down();
//            $myMigrator->runDown($migrationFullPath, null, false);
            $databaseDescribesDown = $this->getDatabaseDescribes();

//            if (json_encode($databaseDescribes) != json_encode($databaseDescribesDown)) {
//                file_put_contents('abc.json', json_encode([
//                    $databaseDescribes,
//                    $databaseDescribesDown,
//                ], JSON_PRETTY_PRINT));
//            }

            $databaseDescribesDot = Arr::dot($databaseDescribes);
            $databaseDescribesUpDot = Arr::dot($databaseDescribesUp);
            $databaseDescribesDownDot = Arr::dot($databaseDescribesDown);

            static::assertTrue(
                json_encode($databaseDescribes) == json_encode($databaseDescribesDown),
                $this->error(
                    $migrationPath,
                    'up and down not match',
                    [
                        'up' => [
                            'from' => array_diff_assoc($databaseDescribesDot, $databaseDescribesUpDot),
                            'to' => array_diff_assoc($databaseDescribesUpDot, $databaseDescribesDot),
                        ],
                        'down' => [
                            'from' => array_diff_assoc($databaseDescribesUpDot, $databaseDescribesDownDot),
                            'to' => array_diff_assoc($databaseDescribesDownDot, $databaseDescribesUpDot),
                        ],
                        'down_missing' => array_diff_assoc(
                            $databaseDescribesDot,
                            $databaseDescribesDownDot
                        ),
                        'down_need_remove' => array_diff_assoc($databaseDescribesDownDot, $databaseDescribesDot),
                    ],
                )
            );

            $databaseDescribes = $databaseDescribesUp;

//            Artisan::call('migrate', ['--path' => $migrationPath]);
            $myMigrator->resolvePath($migrationFullPath)->up();
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getDatabaseDescribes(): array
    {
        $databaseDescribes = [];
        $tables = DB::select('SHOW TABLES');
        $schema = DB::getDoctrineSchemaManager();

        foreach ($tables as $table) {
            $table = implode(json_decode(json_encode($table), true));
            $auditTable = AuditTable::make($table);
            $columns = $schema->listTableColumns($table);

            $databaseDescribes[$table] = [];

            foreach ($columns as $column) {
                $databaseDescribes[$table][$column->getName()] = $column->toArray();
                $databaseDescribes[$table][$column->getName()]['type'] = $column->getType()->getName();

                ksort($databaseDescribes[$table]);
            }

            $databaseDescribes[$table]['__index'] = $auditTable->indexes();

            if (empty($databaseDescribes[$table]['__index'])) {
                unset($databaseDescribes[$table]['__index']);
            }
        }

        unset($databaseDescribes['migrations']);
        ksort($databaseDescribes);

        return $databaseDescribes;
    }
}
