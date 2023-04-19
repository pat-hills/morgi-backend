<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateNewAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $users = [
            ['type' => 'admin', 'email' => 'marlene@iml.ad', 'status' => 'accepted', 'referral_code' => Str::uuid(), 'username' => 'marlene_admin', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => bcrypt("aa?r9QAFn!85R+hc")],
            ['type' => 'admin', 'email' => 'mroubo@iml.ad', 'status' => 'accepted', 'referral_code' => Str::uuid(), 'username' => 'mroubo_admin', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => bcrypt("kr4+D%^sAq9ssvTS")],
            ['type' => 'admin', 'email' => 'tony@iml.ad', 'status' => 'accepted', 'referral_code' => Str::uuid(), 'username' => 'tony_admin', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => bcrypt("c69bDgKa+QuHtmHz")],
            ['type' => 'admin', 'email' => 'martin@iml.ad', 'status' => 'accepted', 'referral_code' => Str::uuid(), 'username' => 'martin_admin', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => bcrypt("pmtevaQ$86tV=_2_")],
            ['type' => 'admin', 'email' => 'carlos@iml.ad', 'status' => 'accepted', 'referral_code' => Str::uuid(), 'username' => 'carlos_admin', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => bcrypt("p_*LESg*vVwr4RU-")],
            ['type' => 'admin', 'email' => 'cvillarejo@iml.ad', 'status' => 'accepted', 'referral_code' => Str::uuid(), 'username' => 'cvillarejo_admin', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => bcrypt("NC5sZZ&3=MBj8C&?")],
            ['type' => 'admin', 'email' => 'jmartinez@iml.ad', 'status' => 'accepted', 'referral_code' => Str::uuid(), 'username' => 'jmartinez_admin', 'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => bcrypt("7USDzD?+LaGaEm6&")]
        ];

        foreach ($users as $user){
            \App\Models\User::query()->create($user);
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
