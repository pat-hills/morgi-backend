<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\GoalType;
class AddDefaultsGoalTypesToGoalTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

            GoalType::create([
                'type' => 'SMALL SIZE GOAL',
                'min' => 0,
                'max' => 100,
                'duration_type'=> 'days',
                'duration_value' => 7,
            ]);


            GoalType::create([
                'type' => 'MEDIUM SIZE GOAL',
                'min' => 101,
                'max' => 500,
                'duration_type'=> 'months',
                'duration_value' => 1,
            ]);


            GoalType::create([
                'type' => 'LARGE SIZE GOAL',
                'min' => 500,
                'max' => 10000000,
                'duration_type'=> 'months',
                'duration_value' => 3,
            ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
