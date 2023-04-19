<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ImportDB extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = env('DB_USERNAME');
        $host = env('DB_HOST');
        $password = env('DB_PASSWORD');
        $name = env('DB_DATABASE');

        if(!file_exists('/var/www/html/morgi-backend/database/seeders/db-dump.sql')){
            echo "\nDump file does not exists";
            return;
        }

        exec("mysql -u$user -h$host -p$password $name < /var/www/html/morgi-backend/database/seeders/db-dump.sql");
    }
}
