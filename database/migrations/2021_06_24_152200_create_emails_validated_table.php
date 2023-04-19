<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsValidatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails_validated', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('verdict');
            $table->double('score');
            $table->string('local');
            $table->string('host');
            $table->string('suggestion')->nullable(true);
            $table->boolean('has_valid_address_syntax');
            $table->boolean('has_mx_or_a_record');
            $table->boolean('is_suspected_disposable_address');
            $table->boolean('is_suspected_role_address');
            $table->boolean('has_known_bounces');
            $table->boolean('has_suspected_bounces');
            $table->string('source')->nullable(true);
            $table->string('ip_address');
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
        Schema::dropIfExists('emails_validated');
    }
}
