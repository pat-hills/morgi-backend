<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateUsernameLogic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->after('status');
        });

        $leaders= \App\Models\Leader::all();
        $rookies= \App\Models\Rookie::all();

        foreach ($leaders as $leader){
            \App\Models\User::where('id', $leader->id)->update(['username' => $leader->username]);
        }

        foreach ($rookies as $rookie){
            \App\Models\User::where('id', $rookie->id)->update(['username' => $rookie->username]);
        }


        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('username');
        });
        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('username');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->string('username')->after('id');
        });
        Schema::table('rookies', function (Blueprint $table) {
            $table->string('username')->after('id');
        });

        $users = \App\Models\User::all();

        foreach ($users as $user){
            if($user->type==='rookie'){
                \App\Models\Rookie::where('id', $user->id)->update(['username' => $user->username]);
            }elseif($user->type==='leader'){
                \App\Models\Leader::where('id', $user->id)->update(['username' => $user->username]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
}
