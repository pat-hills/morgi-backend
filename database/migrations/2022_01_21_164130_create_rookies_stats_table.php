<?php

use App\Models\Rookie;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookiesStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rookies_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rookie_id');
            $table->integer('logins_streak')->default(0);
            $table->integer('photos')->default(0);
            $table->integer('videos')->default(0);
            $table->boolean('has_description')->default(0);
            $table->boolean('joined_telegram_bot')->default(0);
            $table->integer('first_micromorgi_gift_leaders')->default(0);
            $table->integer('leaders_referred')->default(0);
            $table->integer('active_subscriptions')->default(0);
            $table->integer('leaders_sending_micromorgi_last_seven_days')->default(0);
            $table->integer('avg_subscriptions_period')->default(0);
            $table->integer('avg_first_response_time_minutes')->default(0);
            $table->timestamps();
        });

        Schema::table('rookies', function (Blueprint $table) {
            $table->integer('logins_streak')->default(0)->after('likely_receive_score');
            $table->integer('first_micromorgi_gift_leaders')->default(0)->after('likely_receive_score');
        });

        $rookies = Rookie::all();
        $rookies_stats = [];
        foreach ($rookies as $rookie){
            $rookies_stats[] = ['rookie_id' => $rookie->id, 'created_at' => now(), 'updated_at' => now()];
        }
        \App\Models\RookieStats::query()->insert($rookies_stats);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('logins_streak');
            $table->dropColumn('first_micromorgi_gift_leaders');
        });

        Schema::dropIfExists('rookies_stats');
    }
}
