<?php

namespace SocolaDaiCa\LaravelAudit\database\seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class LaravelAuditDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
    }
}
