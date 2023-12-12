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

        }
    }

    protected function enableForeignKeysChecks(): void
    {
        $connection = Config::get('DB_CONNECTION');
        if ($connection == 'pgsql') {
            DB::statement("SET session_replication_role = 'origin';");
        } elseif ($connection == 'mysql') {

        }
    }

    protected function TruncateTable($table)
    {
        DB::TABLE($table)->truncate();
    }
}
