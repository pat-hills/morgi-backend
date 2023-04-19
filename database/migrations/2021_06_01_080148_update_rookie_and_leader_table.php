<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRookieAndLeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $rookies = \App\Models\Rookie::where('description', '!=', null)->get()->toArray();
        $leaders = \App\Models\Leader::where('description', '!=', null)->get()->toArray();

        $users = array_merge($rookies, $leaders);

        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('description')->nullable(true)->after('email');
        });

        foreach ($users as $description){

            \App\Models\User::where('id', $description['id'])->update(['description' => $description['description']]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $users = \App\Models\User::where('description', '!=', null)->get()->toArray();

        Schema::table('rookies', function (Blueprint $table) {
            $table->text('description')->nullable(true)->after('last_name');
        });

        Schema::table('leaders', function (Blueprint $table) {
            $table->text('description')->nullable(true)->after('id');
        });

        foreach ($users as $user){

            if($user['type']=='rookie'){
                \App\Models\Rookie::where('id', $user['id'])->update(['description' => $user['description']]);
            }elseif($user['type']=='leader'){
                \App\Models\User::where('id', $user['id'])->update(['description' => $user['description']]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
