<?php

namespace Database\Factories;

use App\Models\User;
use App\Utils\UserUtils;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => rand(1,10) . $this->faker->unique()->userName . Str::random(4),
            'password' => bcrypt('12345678'),
            'activation_token' => md5(uniqid('', true) . rand(1, 10000)),
            'group_id' => 1,
            'referral_code' => rand(1, 10000) . Str::uuid(),
            'unsubscribe_token' => md5(uniqid('', true) . rand(1, 10000)),
            'gender_id' => rand(1,3),
            'pubnub_uuid' => Str::orderedUuid(),
            'currency' => 'USD',
            'signup_country_id' => rand(1,126),
            'cookie_policy' => true,
            'telegram_bot_token' => md5(uniqid('', true) . rand(1, 10000)),
            'email' => rand(1,10) . Str::random(4) . $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'last_activity_at' => now(),
            'active' => 1,
            'language' => 'en-US',
            'type' => 'rookie',
            'status' => 'accepted',
            'description' => $this->faker->realText(60),
        ];
    }
}
