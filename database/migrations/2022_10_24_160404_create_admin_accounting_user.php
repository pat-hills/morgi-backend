<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;


class CreateAdminAccountingUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gender = \App\Models\Gender::query()
            ->where('key_name', 'other')
            ->first();

        User::create([
            'type' => 'admin',
            'email' => 'accounting@pythiaplus.com',
            'status' => 'accepted',
            'username' => 'Accounting',
            'gender_id' => $gender->id ?? 1,
            'email_verified_at' => now(),
            'active' => 1,
            'password' => '$2y$10$s0kF.GMTmoblijcV5R0YnefYKoLIG/AyLX3QcHb7K0ArIo9zNDXyO',
            'referral_code' => rand(1, 10000) . Str::uuid()
        ]);
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
