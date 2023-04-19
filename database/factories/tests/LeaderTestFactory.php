<?php

namespace Database\Factories\tests;

use App\Models\Gender;
use App\Models\Path;
use App\Models\User;
use App\Models\UserABGroup;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

class LeaderTestFactory
{
    public static function first(): array
    {
        $faker = Container::getInstance()->make(Generator::class);

        return [
            'type' => 'leader',
            'password' => '12345678',
            'gender_id' => Gender::query()->where('key_name', '!=', 'unknown')->where('key_name', '!=', 'all')->inRandomOrder()->first()->id,
            'email' => Str::random(4) . $faker->unique()->safeEmail,
            'description' => $faker->realText(60),
            'public_group' => UserABGroup::query()->inRandomOrder()->first()->id,
            'path_id' => Path::query()->where('is_subpath', false)->inRandomOrder()->first()->id,
            'interested_in_gender_id' => Gender::query()->where('key_name', '!=', 'unknown')->inRandomOrder()->first()->id,
            'first_rookie' => User::query()->where('active', true)->where('type', 'rookie')->inRandomOrder()->first()->username ?? null,
            'persona' => rand(1,3)
        ];
    }
}
