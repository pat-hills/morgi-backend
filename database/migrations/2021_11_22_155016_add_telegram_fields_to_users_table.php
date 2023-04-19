<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelegramFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_bot_token')->after('clicked_telegram_link');
            $table->string('telegram_username')->nullable(true)->after('clicked_telegram_link');
            $table->unsignedBigInteger('telegram_user_id')->nullable(true)->after('clicked_telegram_link');
            $table->unsignedBigInteger('telegram_chat_id')->nullable(true)->after('clicked_telegram_link');
            $table->timestamp('joined_telegram_bot_at')->nullable(true)->after('clicked_telegram_link');
        });

        $users = User::all();
        foreach ($users as $user){
            $token = md5(uniqid('', true) . rand(1, 10000));
            $user->update(['telegram_bot_token' => $token]);
        }

        \App\Models\NotificationType::query()->create(['user_type' => 'both',
            'type' => 'telegram_bot', 'title' => 'Welcome to Morgi!',
            'content' => 'It is very important to connect to the Telegram bot and receive an immediate push notification that’ll help you stay responsive!']);

        Schema::table('rookies_points', function (Blueprint $table) {
            $table->integer('telegram_bot')->default(0)->after('morgi');
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->integer('telegram_bot')->default(0)->after('morgi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telegram_bot_token');
            $table->dropColumn('joined_telegram_bot_at');
            $table->dropColumn('telegram_user_id');
            $table->dropColumn('telegram_chat_id');
            $table->dropColumn('telegram_username');
        });

        Schema::table('rookies_points', function (Blueprint $table) {
            $table->dropColumn('telegram_bot');
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->dropColumn('telegram_bot');
        });
    }
}
