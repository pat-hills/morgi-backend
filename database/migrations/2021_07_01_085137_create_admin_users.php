<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\User::where('email', 'root@root.com')->delete();

        $users = [
            ['type' => 'admin', 'email' => 'kromin@morgi.org', 'status' => 'accepted', 'username' => 'KrominTeam', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$EcfodC5Yz5LhS5Q09gtrgObtak.QuwP5VcZJtkyaRWjtmuUE4krkW'],
            ['type' => 'admin', 'email' => 'muly@litvak.com', 'status' => 'accepted', 'username' => 'MulyLitvak', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$w52HKHhy5zWGkWrGZqh69e0Ci3gcxgYJkGYa2d26yHKNEGq8SdKby'],
            ['type' => 'admin', 'email' => 'ariels@top4.com', 'status' => 'accepted', 'username' => 'ArielsSzmuszkowicz', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$ajF5TvXYFdu/L7OfSI0SoO8G.gQIjiwZHosRVcvCqPcx8h7yaXmmu'],
            ['type' => 'admin', 'email' => 'estie@top4.com', 'status' => 'accepted', 'username' => 'EstiEliyhu', 'gender_id' => 2, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$9NBeDRyvTmhGHsR5NiF.w.GeAFoqqK.6jDaa/vCzVYAeM.Ud8Ok2i'],
            ['type' => 'admin', 'email' => 'stuart@iml.ad', 'status' => 'accepted', 'username' => 'StuartFoster', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$QF2GmLcNjRSRQTPmDdaNA.O3PTiJAShWtjM8yb.nr.2kFEE8vq4tm'],
            ['type' => 'admin', 'email' => 'amirr@top4.com', 'status' => 'accepted', 'username' => 'AmirRejuan', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$MBD7eU4EbOpmh5HzVkV6ZO0YQoGY8IotH6LjVdaJdYNN4VT4Rw9Aa']
        ];

        foreach ($users as $user){
            \App\Models\User::create($user);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
