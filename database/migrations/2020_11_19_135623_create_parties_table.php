<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rookie_id');
            $table->text('description');
            $table->enum('type', ['party', 'parade']);
            $table->integer('expected_guests');
            $table->string('guests_types', 255); //?
            $table->enum('purpose', ['promote', 'help', 'other']);
            $table->string('purpose_reason', 255);
            $table->string('purpose_number', 255);
            $table->integer('add_merch');
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
        Schema::dropIfExists('parties');
    }
}
