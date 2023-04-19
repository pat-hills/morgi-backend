<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * This is not pushed
     * @return void
     */
    public function run()
    {

        
        if(env('APP_ENV') !== 'prod'){
            $this->call([
                ImportDB::class
            ]);
        }

    }
}
