<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGivebacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('givebacks', function (Blueprint $table) {
            $table->id();
            $table->integer('total_subscriptions_count');
            $table->integer('micromorgi');
            $table->timestamps();
        });

        $givebacks = [
            ['total_subscriptions_count' => 1, 'micromorgi' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['total_subscriptions_count' => 3, 'micromorgi' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['total_subscriptions_count' => 6, 'micromorgi' => 75, 'created_at' => now(), 'updated_at' => now()],
            ['total_subscriptions_count' => 10, 'micromorgi' => 30, 'created_at' => now(), 'updated_at' => now()],
        ];

        \App\Models\Giveback::query()->insert($givebacks);

        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_subscriptions_count')->default(0)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('givebacks');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_subscriptions_count');
        });
    }
}
