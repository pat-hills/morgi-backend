<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixGoalTypesStandard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    private $strings = [
        'SMALL SIZE GOAL',
        'MEDIUM SIZE GOAL',
        'LARGE SIZE GOAL',
    ];

    public function up()
    {
        foreach ($this->strings as $string) {
            $slug = str_replace(' ','_',strtolower($string));
            \App\Models\GoalType::query()->where('type', $string)->update(['type' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->strings as $string) {
            $slug = str_replace(' ','_',strtolower($string));
            \App\Models\GoalType::query()->where('type', $slug)->update(['type' => $string]);
        }
    }
}
