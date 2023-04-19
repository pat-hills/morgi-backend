<?php

namespace Database\Seeders;

use App\Utils\User\UserTestUtils;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leaders_to_create = 1000;
        $rookies_to_create = 1000;

        echo ("Start creating Leaders.\n");

        for ($i = 0; $i < $leaders_to_create; $i++) {
            UserTestUtils::create('leader');
            $index = $i + 1;
            echo ("Created {$index}* leader.\n");
            sleep(1);
        }

        echo ("Start creating Rookies.\n");

        for ($i = 0; $i < $rookies_to_create; $i++) {
            UserTestUtils::create('rookie');
            $index = $i + 1;
            echo ("Created {$index}* rookie.\n");
            sleep(1);
        }
    }
}
