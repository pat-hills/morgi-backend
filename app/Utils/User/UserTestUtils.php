<?php

namespace App\Utils\User;

use App\Enums\UserDescriptionHistoryEnum;
use App\Enums\UserEnum;
use App\Mixpanel\Events\EventAdminAcceptedUser;
use App\Models\Country;
use App\Models\Gender;
use App\Models\Path;
use App\Models\User;
use App\Models\UserABGroup;
use App\Models\UserDescriptionHistory;
use App\Utils\NotificationUtils;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserTestUtils
{
    public static function create($type): array
    {
        $path = env('APP_URL') . '/v2';
        $password = '12345678';
        $photo_path = 'cristian_belli_face.jpg';

        $photo = file_get_contents(resource_path($photo_path));
        $response = Http::attach('photo', $photo, $photo_path)->post($path . '/photo');
        $photo_path_location = $response->json()['path_location'];

        $faker = Container::getInstance()->make(Generator::class);
        $fake_user = [
            'type' => $type,
            'password' => $password,
            'password_confirmation' => $password,
            'gender_id' => Gender::query()->where('key_name', '!=', 'unknown')->where('key_name', '!=', 'all')->inRandomOrder()->first()->id,
            'email' => 'test_user' . Str::random(4) . $faker->unique()->safeEmail,
            'description' => $faker->realText(60),
            'public_group' => UserABGroup::query()->inRandomOrder()->first()->id,
            'path_id' => Path::query()->where('is_subpath', false)->inRandomOrder()->first()->id,
            'interested_in_gender_id' => Gender::query()->where('key_name', '!=', 'unknown')->inRandomOrder()->first()->id,
            'first_rookie' => User::query()->where('active', true)->where('type', 'rookie')->inRandomOrder()->first()->username ?? null,
            'persona' => rand(1,3),
            'age_confirmation' => true,
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'path_location' => $photo_path_location,
            'birth_date' => Carbon::create($faker->dateTimeBetween(Carbon::createFromDate('1990-10-13'), Carbon::createFromDate('2001-10-13')))->toDateString(),
            'country_id' => Country::query()->inRandomOrder()->first()->id,
        ];

        $response = Http::post($path . '/auth/signup',  $fake_user);

        $user_id = $response->json()['id'];
        $user = User::query()->where('id', $user_id)->first();
        $admin = User::query()->where('type', 'admin')->first();

        Http::get("{$path}/auth/signup/activate/{$user->activation_token}");

        if ($type === 'rookie') {
            UserDescriptionHistory::query()
                ->where('user_id', $user->id)
                ->orderBy('id', 'DESC')
                ->first()
                ->update([
                    'status' => UserDescriptionHistoryEnum::STATUS_APPROVED,
                    'admin_id' => $admin->id
                ]);

            $user->update(['email_verified_at' => now()]);
            $user->update([
                'status' => UserEnum::STATUS_ACCEPTED,
                'admin_id' => $admin->id, 'admin_check' => 0
            ]);

            $user->createUserStatusHistory(UserEnum::STATUS_ACCEPTED, $admin->username, "Email verified");

            NotificationUtils::sendNotification($user->id, 'description_approved', now());
            NotificationUtils::sendNotification($user->id, 'user_accepted', now());
            try {
                EventAdminAcceptedUser::config($user->id);
            } catch (\Exception $exception) {
            }
        }

        return [
            'email' => $user->email,
            'password' => $password
        ];
    }
}
