<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('min', 10);
            $table->decimal('max', 10);
            $table->enum('currency_type', ['morgi', 'micro_morgi'])->default('micro_morgi');
            $table->enum('duration_type', ['days','weeks', 'months'])->default('days');
            $table->integer('duration_value');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goal_types');
    }
}
