<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Path;
use App\Models\Photo;
use App\Models\Rookie;
use App\Models\RookieScore;
use App\Models\RookieStats;
use App\Models\User;
use App\Models\UserPath;
use App\Services\Chat\Chat;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RookiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $photos = [
            'photos/2021-07-14/3_photo_SH7JVmOs7vG8_c.jpg',
            'photos/2021-07-14/3_photo_eSoVuvwSIwk5_c.jpg',
            'photos/2021-07-14/3_photo_UOwXo9269Taa_c.jpg',
            'photos/2021-07-14/3_photo_cC65JNCBdRRP_c.jpg',
            'photos/2021-07-14/3_photo_aHNVjWqB4KZg_c.jpg'
        ];

        DB::beginTransaction();

        try {

            $users = User::factory()->count(1000)->create();

            $rookies_to_insert = [];
            $rookie_score_to_insert = [];
            $photos_to_insert = [];
            $users_paths_to_insert = [];
            $users_subpaths_to_insert = [];

            $paths = Path::where('is_subpath', false)->get();

            foreach ($users as $user){

                $faker = Factory::create();
                $birth_date = $faker->dateTimeBetween(Carbon::createFromDate('1990-10-13'), Carbon::createFromDate('2001-10-13'));
                $country_id = Country::query()->inRandomOrder()->first()->id;

                $rookies_to_insert[] = ['id' => $user->id, 'first_name' => $faker->firstName, 'last_name' => $faker->lastName,
                    'birth_date' => $birth_date, 'country_id' => $country_id, 'region_id' => 1, 'age_confirmation' => true, 'created_at' => now(), 'updated_at' => now()];

                $rookie_score_to_insert[] = ['rookie_id' => $user->id, 'created_at' => now(), 'updated_at' => now()];
                $photos_to_insert[] = ['user_id' => $user->id, 'path_location' => $photos[rand(0,4)], 'main' => 1, 'created_at' => now(), 'updated_at' => now()];
                $photos_to_insert[] = ['user_id' => $user->id, 'path_location' => $photos[rand(0,4)], 'main' => 0, 'created_at' => now(), 'updated_at' => now()];
                $photos_to_insert[] = ['user_id' => $user->id, 'path_location' => $photos[rand(0,4)], 'main' => 0, 'created_at' => now(), 'updated_at' => now()];

                Chat::config($user->id)->userSignup($user);

                $path = $paths->random(1)->first()->id;
                $users_paths_to_insert[] = ['user_id' => $user->id, 'path_id' => $path, 'created_at' => now(), 'updated_at' => now()];

                $subpath = Path::where('parent_id', $path)->where('is_subpath', true);
                if($subpath->count()!=0){
                    $users_subpaths_to_insert[] = ['user_id' => $user->id, 'path_id' => $subpath->get()->random(1)->first()->id, 'is_subpath' => true, 'created_at' => now(), 'updated_at' => now()];
                }
            }

            Rookie::query()->insert($rookies_to_insert);
            RookieScore::query()->insert($rookie_score_to_insert);
            RookieStats::query()->insert($rookie_score_to_insert);
            Photo::query()->insert($photos_to_insert);
            UserPath::query()->insert($users_paths_to_insert);
            UserPath::query()->insert($users_subpaths_to_insert);

            DB::commit();

        }catch (\Exception $exception){

            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }
    }
}
