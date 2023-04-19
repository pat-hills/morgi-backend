<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnknowGenderType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gender = \App\Models\Gender::create([
            'name' => 'Unknown',
            'key_name' => 'unknown'
        ]);

        \App\Models\User::query()->where('status', 'deleted')->update(['gender_id' => $gender->id]);
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
