<?php

namespace SocolaDaiCa\LaravelAudit\Tests\Database;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SocolaDaiCa\LaravelAudit\Tests\TestCase;

class DatabaseTest extends TestCase
{
    public function testX()
    {
        static::assertTrue(true);
    }
//    public function test_database_missing_table()
//    {
//        $x = require(__DIR__.'/../../config/socoladaica/audit.php');
////        dd($x['database']['tables']);
////
////        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
////        dd($tables);
//
//        $basicDesignTables = array_keys($x['database']['tables']);
//        $databaseTables = array_values(DB::connection()->getDoctrineSchemaManager()->listTableNames());
//        $basicDesignMissingTables = array_values(array_diff($databaseTables, $basicDesignTables));
//        $databaseMissingTables = array_values(array_diff($basicDesignTables, $databaseTables));
//
//        $this->assertEmpty($basicDesignMissingTables, $this->echo('basicDesignMissingTables', $basicDesignMissingTables));
//        $this->assertEmpty($databaseMissingTables,$this->echo('$databaseMissingTables', $databaseMissingTables));
//
//        foreach ($x['database']['tables'] as $table => $columns) {
//            $databaseColumns = Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($table);
//
//            $basicDesignMissingColumns = array_diff(
//                array_values(array_keys($databaseColumns)),
//                $columns
//            );
//            $basicDesignMissingColumns = array_values($basicDesignMissingColumns);
//
//            $databaseMissingColumns = array_diff(
//                $columns,
//                array_values(array_keys($databaseColumns))
//            );
//            $databaseMissingColumns = array_values($databaseMissingColumns);
//
//
//            $this->assertEmpty(
//                $basicDesignMissingColumns,
//                $this->echo("\$basicDesignMissingColumns[{$table}]", $basicDesignMissingColumns)
//            );
//
//            $this->assertEmpty(
//                $databaseMissingColumns,
//                $this->echo("\$databaseMissingColumns[{$table}]", $databaseMissingColumns)
//            );
//        }
//    }

//    public function testCanRollBack()
//    {
//        $this->assertTrue(true);
//    }
//
//    public function testDatabaseDesign()
//    {
//        Schema::shouldReceive('create')->andReturn(null);
////        \Schema::shouldReceive('create')->andReturn('sss');
////        $y = $this->createMock(Builder::class)->getMock();
////        $y->method('create')->willReturn(function ($table, Closure $callback) {
////            dd('xxx');
////        });
////
////        Artisan::call('migrate');
//    }

//    public function testColumnName()
//    {
//        $schema = DB::getDoctrineSchemaManager();
//        $tables = \DB::select('SHOW TABLES');
//
//        $columnsWrongFormat = [];
//        foreach ($tables as $table) {
//            $table = implode(json_decode(json_encode($table), true));
//
//            $columns = $schema->listTableColumns($table);
//
//            foreach ($columns as $column) {
//                if (preg_match('/[a-z_]*/', $column->getName()) == false) {
//                    continue;
//                }
//
//                $columnsWrongFormat[$table][] = $column->getName();
//            }
//        }
//
//        $this->assertEmpty(
//            $columnsWrongFormat,
//            $this->error(
//                'column should be snake_case',
//                $columnsWrongFormat,
//            )
//        );
//    }
}
