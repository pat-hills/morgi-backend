<?php

use App\Enums\UserEnum;
use App\Models\City;
use App\Models\Country;
use App\Models\Path;
use App\Models\Region;
use App\Models\Rookie;
use App\Models\User;
use App\Models\UserPath;
use Database\Factories\tests\RookieTestFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class, \Illuminate\Foundation\Testing\WithoutMiddleware::class);

$path = '/v2';
$email = Str::random(10) . 'test@gmail.com';
$password = '12345678';

test('Rookie Flow', function () use ($email, $password, $path)
{
    $admin_email = User::query()->where('type', 'leader')->first()->email;

    //StorePhoto
    $photo = ['photo' => new UploadedFile(resource_path('cristian_belli_face.jpg'), 'cristian_belli_face.jpg', null , null , true, true)];
    $response = $this->postJson($path . '/photo', $photo);
    $photo_path_location = $response->getData()->path_location;

    //Sign up
    $rookie = RookieTestFactory::first();
    $rookie['email'] = $email;
    $rookie['password'] = $password;
    $rookie['password_confirmation'] = $password;
    $rookie['path_location'] = $photo_path_location;

    $response = $this->post($path . '/auth/signup', $rookie);

    $response->assertStatus(201);
    $this->assertEquals($rookie['email'], $response->getData()->email);


    //Verify email Sign Up
    $rookie_user = User::query()->where('email', $email)->first();
    $this->assertNotNull($rookie_user);

    $response = $this->get($path . '/auth/signup/activate/' . $rookie_user->activation_token);
    $response->assertStatus(200);

    $this->assertEquals($email, $response->getData()->email);


    //Activation rookie by admin
    $admin = User::query()->where('email', $admin_email)->first();
    $rookie_user->createUserStatusHistory(UserEnum::STATUS_ACCEPTED, $admin->username, 'test');
    $rookie_user->update(['status' => UserEnum::STATUS_ACCEPTED, 'admin_id' => Auth::id()]);

    $rookie = Rookie::query()->where('id', $rookie_user->id)->first();
    $this->assertNotNull($rookie);

    //Login
    $data = [
        'email' => $email,
        'password' => $password
    ];

    $response = $this->post($path . '/auth/login', $data);
    $response->assertStatus(200);


    //Index Notification
    $access_token = $response->getData()->access_token;
    $headers = [
        'Accept' => 'application/json',
        'Authorization' => "Bearer $access_token"
    ];

    $response = $this->withHeaders($headers)->get($path . '/notifications');
    $notifications_types = [];

    foreach ($response->getData()->data as $notification) {
        $notifications_types[] = $notification->notification_type->type;
    }

    foreach ($notifications_types as $notification_type) {
        $this->assertContains($notification_type, ['telegram_bot', 'rookie_login']);
    }

    $response->assertStatus(200);


    //Rookie update test
    $region = Region::query()->first();
    $first_name_to_update = 'Test';
    $last_name_to_update = 'Test1';
    $phone_number_to_update = '3333333333';
    $apartment_number_to_update = '1';
    $street_to_update = 'test street';
    $zip_code_to_update = '00000';
    $country_id_to_update = Country::query()->first()->id;
    $birth_date_to_update = Carbon::now()->toDateString();
    $region_name_to_update = $region->name;
    $subpath_id_to_update = Path::query()->where('is_subpath', true)->first()->id;

    $data = [
        'first_name' => $first_name_to_update,
        'last_name' => $last_name_to_update,
        'birth_date' => $birth_date_to_update,
        'country_id' => $country_id_to_update,
        'zip_code' => $zip_code_to_update,
        'street' => $street_to_update,
        'apartment_number' => $apartment_number_to_update,
        'phone_number' => $phone_number_to_update,
        'region' => $region_name_to_update,
        'subpath_id' => $subpath_id_to_update
    ];

    $response = $this->post($path . '/profile/update' , $data);
    $response->assertStatus(200);
    $rookie = Rookie::query()->where('id', $rookie->id)->first();

    $this->assertEquals($rookie->first_name, $first_name_to_update);
    $this->assertEquals($rookie->last_name, $last_name_to_update);
    $this->assertEquals($rookie->birth_date, $birth_date_to_update);
    $this->assertEquals($rookie->country_id, $country_id_to_update);
    $this->assertEquals($rookie->zip_code, $zip_code_to_update);
    $this->assertEquals($rookie->street, $street_to_update);
    $this->assertEquals($rookie->apartment_number, $apartment_number_to_update);
    $this->assertEquals($rookie->phone_number, $phone_number_to_update);
    $this->assertEquals($rookie->region_name, $region_name_to_update);

    $userpath = UserPath::query()
        ->where('user_id', $rookie->id)
        ->where('path_id', $subpath_id_to_update)
        ->where('is_subpath', true)->first();

    $this->assertNotNull($userpath);



    //Shows test
    $response = $this->withHeaders($headers)->get($path . '/contents/inspiration?page=1&limit=1');
    $response->assertStatus(200);

    $response = $this->withHeaders($headers)->get($path . '/rookies/winners?page=1&limit=3');
    $response->assertStatus(200);

    $response = $this->withHeaders($headers)->get($path . '/rookies/of-the-day?limit=1&page=1');
    $response->assertStatus(200);

    $response = $this->withHeaders($headers)->get($path . '/contents/news-update?page=1&limit=1');
    $response->assertStatus(200);
});
