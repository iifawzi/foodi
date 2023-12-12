<?php

namespace Database\Seeders\traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

trait SeederHelper
{
    protected function disableForeignKeysChecks(): void
    {
        $connection = Config::get('database.default');
        if ($connection == 'pgsql') {
            DB::statement("SET session_replication_role = 'replica'");
        } elseif ($connection == "mysql") {
            DB::statement("SET FOREIGN_KEY_CHECKS=0;");
        } elseif($connection == 'sqlite') {
            DB::statement("PRAGMA ignore_check_constraints = 0;");
        }
    }

    protected function enableForeignKeysChecks(): void
    {
        $connection = Config::get('DB_CONNECTION');
        if ($connection == 'pgsql') {
            DB::statement("SET session_replication_role = 'origin';");
        } elseif ($connection == 'mysql') {
            DB::statement("SET FOREIGN_KEY_CHECKS=1;");
        } elseif($connection == 'sqlite') {
            DB::statement("PRAGMA ignore_check_constraints = 1;");
        }
    }

    protected function TruncateTable($table)
    {
        DB::TABLE($table)->truncate();
    }
}
