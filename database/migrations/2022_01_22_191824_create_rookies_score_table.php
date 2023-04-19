<?php

use App\Models\Rookie;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookiesScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rookies_score', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rookie_id');
            $table->integer('logins_streak')->default(0);
            $table->integer('photos')->default(0);
            $table->integer('videos')->default(0);
            $table->integer('has_description')->default(0);
            $table->integer('joined_telegram_bot')->default(0);
            $table->integer('first_micromorgi_gift_leaders')->default(0);
            $table->integer('leaders_referred')->default(0);
            $table->integer('active_subscriptions')->default(0);
            $table->integer('leaders_sending_micromorgi_last_seven_days')->default(0);
            $table->integer('avg_subscriptions_period')->default(0);
            $table->integer('avg_first_response_time_minutes')->default(0);
            $table->double('morgi_last_seven_days', 8, 2)->default(0);
            $table->double('micromorgi_last_seven_days', 8, 2)->default(0);
            $table->timestamps();
        });

        $rookies = Rookie::all();
        $rookies_score = [];
        foreach ($rookies as $rookie){
            $rookies_score[] = ['rookie_id' => $rookie->id, 'created_at' => now(), 'updated_at' => now()];
        }
        \App\Models\RookieScore::query()->insert($rookies_score);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rookies_score');
    }
}
