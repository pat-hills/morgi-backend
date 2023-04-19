<?php

namespace Tests;

use App\Models\Leader;
use App\Models\User;
use App\Models\Rookie;
use App\Models\UserPath;
use App\Utils\User\Auth\AuthUtils;
use App\Utils\User\UserTestUtils;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;
    protected function setUp(): void
    {
        parent::setUp();
        //$this->artisan('db:seed');
        //$this->artisan('passport:install');
        //run the above to seed your database for the first
    }


    public function getRookie($rookie_id = null)
    {
        // Let's not fill the database with users. The more tests we have the more the DB will get filled
        if(empty($rookie)){
            $randomNo = mt_rand(0, 10000000);
            $user = User::create([
                "type" => "rookie",
                "status" => "accepted",
                "active" => true,
                "username" => 'test' . Str::random() . $this->faker()->userName,
                "email" => 'test' . Str::random() . $this->faker()->safeEmail,
                "gender_id" => 1,
                "password" => Hash::make('password'),
                "telegram_bot_token" => "391e27bfa4079dfa8bec10d9e786d3eb",
                "signup_country_id" => 239,
                "language" => "en-US",
                "currency" => "USD",
                "referral_code" => "340935f2bd4c-c9d0-454b-a011-2d2542cdd0de$randomNo",
            ]);
            Rookie::create([
                'id' => $user->id,
                'user_id' => $user->id,
                'country_id' => 1,
                'first_name' => 'test' . Str::random() . 'RookieFirst',
                'last_name' =>  'test' . Str::random() . 'RookieLast',
                'birth_date' => '1997-07-07'
            ]);
            UserPath::create([
                'is_subpath' => 0,
                'user_id' => $user->id,
                'path_id' => 1
            ]);
        } else {
            $user = User::find($rookie->id);
        }

        $request = new Request();
        $request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36');
        $request->headers->set('ip', '127.0.0.1');

        $login = AuthUtils::login($request, $user, 'Personal');
        $user->accessToken = $login['access_token'];
        return $user;
    }

    public function getLeader($leader_id = null)
    {
        if (empty($leader_id)) {
            $randomNo = mt_rand(0, 10000000);
            $user = User::create([
                "type" => "leader",
                "status" => "accepted",
                "active" => true,
                "username" => 'test' . Str::random() . $this->faker()->userName,
                "email" => 'test' . Str::random() .$this->faker()->safeEmail,
                "gender_id" => 1,
                "password" => Hash::make('password'),
                "telegram_bot_token" => "391e27bfa4079dfa8bec10d9e786d3eb",
                "signup_country_id" => 239,
                "language" => "en-US",
                "currency" => "USD",
                "referral_code" => "340935f2bd4c-c9d0-454b-a011-2d2542cdd0de$randomNo",
            ]);
            Leader::create([
                'id' => $user->id,
                'user_id' => $user->id,
                'country_id' => 1,
                'first_name' => 'test' . Str::random() . 'LeaderFirst',
                'last_name' =>  'test' . Str::random() . 'LeaderLast',
                'birth_date' => '1997-07-07'
            ]);
            UserPath::create([
                'is_subpath' => 0,
                'user_id' => $user->id,
                'path_id' => 1
            ]);
        } else {
            $user = User::find($leader_id);
        }
        $request = new Request();
        $request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36');
        $request->headers->set('ip', '127.0.0.1');

        $login = AuthUtils::login($request, $user, 'Personal');
        $user->accessToken = $login['access_token'];
        return $user;
    }
}
