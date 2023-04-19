<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateNewAdminsCs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        try {

            if(env('APP_ENV') !== 'prod'){

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'yoanna',
                    'email' => 'yoanna@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$j6nmnjFbCG7F.5eJi13zDOBglswrD0e4/EheiGsirUxOorDIqP8fG',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()

                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'jeroen',
                    'email' => 'Jeroen@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$dxLr/bU8iSUqmLBINuVp1.2RqP1tZwSTwsB0SYvRBZc0kHYGuXlc6',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'matidle',
                    'email' => 'mcorrea@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$0bOlJCRehvGAgz1TMoOWoeYuwCFLs9rb.rQ6GFoIwbMear7AKH.ta',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'jorge',
                    'email' => 'Jorge@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$8OMBwo9sZgRAUtlDS4wn/eYKr2pSNOEZcSgRMXgZOUxpRUtWiIjkK',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'georgina',
                    'email' => 'gking@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$a1cPuJIh5ysBMKiojpABru3ScILyJ1YoGNMbaKTwX4mESPXcVR6hW',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'valts',
                    'email' => 'valts@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$UaO5jRwmj1Qi6FCOs9oTEeAjeyW3/KskiYmM04IcdaEzP4E2mUQGa',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'evan',
                    'email' => 'evan@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$u2PJb40iCN.wN.g1ll69HuITyl803FaPPA/Y7LXvpCzfQIT9riLeu',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'lyndsey',
                    'email' => 'lyndsey@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$LkBQXU3NRDRorg9TzJZwDu3fMQMIO887XzulpwbdyRtEZa3aZ.lI.',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'miquel',
                    'email' => 'mfreixas@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$9aEQ3hQxNXexO6PFWjAo2e2inrTQpgwh6CIfY/OM7lyC2klokTVLG',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'gemma',
                    'email' => 'gemma@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$zKWGVgmJngEnYrkyazRCTemZDg.F7jVFJvRlUhkYJBTF8XdAXSi6y',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'liza',
                    'email' => 'liza@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$tmvwQ5Fmpqwd5pdme/uc6eZafDm.v1o91Sk7HM3uy/yENj624dLXi',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'pilar',
                    'email' => 'pilar@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$UD31jO0PJMcfwzhj1PujvOISoCtw7FF7CTn3hQBz/LVUCNbNXZBZ.',
                    'active' => 1,
                    'signup_country_id' => 0,
                    'referral_code' => rand(1, 10000) . Str::uuid(),
                    'pubnub_uuid' => Str::orderedUuid()
                ]);

            }else {

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'yoanna',
                    'email' => 'yoanna@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$j6nmnjFbCG7F.5eJi13zDOBglswrD0e4/EheiGsirUxOorDIqP8fG',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'jeroen',
                    'email' => 'Jeroen@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$dxLr/bU8iSUqmLBINuVp1.2RqP1tZwSTwsB0SYvRBZc0kHYGuXlc6',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'matidle',
                    'email' => 'mcorrea@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$0bOlJCRehvGAgz1TMoOWoeYuwCFLs9rb.rQ6GFoIwbMear7AKH.ta',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'jorge',
                    'email' => 'Jorge@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$8OMBwo9sZgRAUtlDS4wn/eYKr2pSNOEZcSgRMXgZOUxpRUtWiIjkK',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'georgina',
                    'email' => 'gking@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$a1cPuJIh5ysBMKiojpABru3ScILyJ1YoGNMbaKTwX4mESPXcVR6hW',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'valts',
                    'email' => 'valts@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$UaO5jRwmj1Qi6FCOs9oTEeAjeyW3/KskiYmM04IcdaEzP4E2mUQGa',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'evan',
                    'email' => 'evan@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$u2PJb40iCN.wN.g1ll69HuITyl803FaPPA/Y7LXvpCzfQIT9riLeu',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'lyndsey',
                    'email' => 'lyndsey@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$LkBQXU3NRDRorg9TzJZwDu3fMQMIO887XzulpwbdyRtEZa3aZ.lI.',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'miquel',
                    'email' => 'mfreixas@iml.ad',
                    'gender_id' => 1,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$9aEQ3hQxNXexO6PFWjAo2e2inrTQpgwh6CIfY/OM7lyC2klokTVLG',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'gemma',
                    'email' => 'gemma@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$zKWGVgmJngEnYrkyazRCTemZDg.F7jVFJvRlUhkYJBTF8XdAXSi6y',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'liza',
                    'email' => 'liza@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$tmvwQ5Fmpqwd5pdme/uc6eZafDm.v1o91Sk7HM3uy/yENj624dLXi',
                    'active' => 1
                ]);

                User::query()->create([
                    'type' => 'admin',
                    'status' => 'accepted',
                    'username' => 'pilar',
                    'email' => 'pilar@iml.ad',
                    'gender_id' => 2,
                    'email_verified_at' => now(),
                    'password' => '$2y$10$UD31jO0PJMcfwzhj1PujvOISoCtw7FF7CTn3hQBz/LVUCNbNXZBZ.',
                    'active' => 1
                ]);
            }

            DB::commit();
        }catch (Exception $exception){

            DB::rollBack();
            throw new Exception($exception->getMessage());
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
