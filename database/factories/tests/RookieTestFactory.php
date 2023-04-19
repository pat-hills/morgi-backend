<?php

namespace Database\Factories\tests;

use App\Models\Country;
use App\Models\Gender;
use App\Models\Path;
use App\Models\User;
use App\Models\UserABGroup;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

class RookieTestFactory
{
    public static function first(): array
    {
        $faker = Container::getInstance()->make(Generator::class);

        return [
            'type' => 'rookie',
            'password' => '12345678',
            'gender_id' => Gender::query()->where('key_name', '!=', 'unknown')->where('key_name', '!=', 'all')->inRandomOrder()->first()->id,
            'email' => Str::random(4) . $faker->unique()->safeEmail,
            'description' => $faker->realText(60),
            'public_group' => UserABGroup::query()->inRandomOrder()->first()->id,
            'age_confirmation' => true,
            'subpath' => $faker->jobTitle,
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'birth_date' => Carbon::create($faker->dateTimeBetween(Carbon::createFromDate('1990-10-13'), Carbon::createFromDate('2001-10-13')))->toDateString(),
            'country_id' => Country::query()->inRandomOrder()->first()->id,
            'path_id' => Path::query()->where('is_subpath', false)->inRandomOrder()->first()->id
        ];
    }
}
