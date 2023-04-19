<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Str;

class MakeReferralUniqueToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral');
            $table->string('referral_code')->nullable()->unique()->after('group_id');
        });

        $users = User::all();
        foreach ($users as $user){
            $user->update(["referral_code" => rand(1, 10000) . Str::uuid()]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable(false)->change();
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
            $table->dropColumn('referral_code');
            $table->integer('referral')->nullable()->after('group_id');
        });
    }
}
