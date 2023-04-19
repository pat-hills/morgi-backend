<?php

use App\Models\Country;
use App\Models\Leader;
use App\Models\LeaderPath;
use App\Models\Path;
use App\Models\PubnubMessage;
use App\Models\RookieSaved;
use App\Models\RookieSeen;
use App\Models\RookieSeenHistory;
use App\Models\User;
use Database\Factories\tests\LeaderTestFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

$path = '/v2';
$email = Str::random(10) . 'test@gmail.com';
$password = '12345678';
$user_json_structure = [
    'data' => [[
        'id',
        'type',
        'full_name',
        'username',
        'description',
        'is_online',
        'pubnub_uuid',
        'created_at',
        'gender' => [
            'id',
            'name',
            'key_name'
        ],
        'avatar' => [
            'id',
            'user_id',
            'url',
            'path_location',
            'main',
            'created_at',
            'under_validation'
        ],
        'type_attributes' => [
            'id',
            'first_name',
            'last_name',
            'birth_date',
            'is_converter',
            'has_past_goals',
            'goals' => [],
            'photos_count',
            'photos' => [],
            'country',
            'path',
            'subpath'
        ]
    ]]];
$persona = 'a';

test('Leader Flow', function () use ($email, $password, $path, $user_json_structure, $persona)
{
    //Sign Up
    $leader = LeaderTestFactory::first();
    $leader['email'] = $email;
    $leader['password'] = $password;
    $leader['password_confirmation'] = $password;
    $leader['persona'] = $persona;

    $leader_path = Path::query()->where('is_subpath', false)->first();
    $leader['path_id'] = $leader_path->id;

    $rookie = User::query()
        ->where('type', 'rookie')
        ->where('active', true)
        ->first();
    $leader['first_rookie'] = $rookie->username;

    $response = $this->post($path . '/auth/signup', $leader);
    $response->assertStatus(201);
    $this->assertEquals($leader['email'], $response->getData()->email);


    //Activation Sign Up
    $leader_user = User::query()->where('email', $email)->first();
    $leader = Leader::where('id', $leader_user->id)->first();
    $this->assertNotNull($leader_user);

    $response = $this->get($path . '/auth/signup/activate/' . $leader_user->activation_token);
    $response->assertStatus(200);

    $is_leader_active = User::query()->where('id', $leader->id)->first()->active;

    $this->assertTrue($is_leader_active);
    $this->assertEquals($email, $response->getData()->email);


    //Login
    $data = [
        'email' => $email,
        'password' => $password
    ];

    $response = $this->post($path . '/auth/login', $data);
    $response->assertStatus(200);


    //Create LeaderPath on Sign Up
    $new_leader_main_path = LeaderPath::query()
        ->where('leader_id',$leader->id)
        ->where('is_main', true)
        ->first();
    $this->assertNotNull($new_leader_main_path);
    $this->assertEquals($leader_path->id, $new_leader_main_path->path_id);


    //First Rookie on Sign Up
    $this->assertEquals($leader->first_rookie, $rookie->id);


    //Persona on Sign Up
    $this->assertEquals($leader_user->persona, $persona);

    //Index Notification
    $access_token = $response->getData()->access_token;
    $headers = [
        'Accept' => 'application/json',
        'Authorization' => "Bearer $access_token"
    ];

    $response = $this->withHeaders($headers)->get($path . '/notifications');
    $notification_type = $response->getData()->data[0]->notification_type->type;

    $response->assertStatus(200);
    $this->assertEquals($notification_type, 'leader_login');


    //Update Leader
    $username_to_update = "usernametest";
    $gender_id_to_update = 1;
    $gender_interest_to_update = 1;
    $carousel_type_to_update = 'vertical';
    $json_to_send = [
        'username' => $username_to_update,
        'gender_id' => $gender_id_to_update,
        'interested_in_gender_id' => $gender_interest_to_update,
        'carousel_type' => $carousel_type_to_update
    ];

    $response = $this->withHeaders($headers)->post($path . '/profile/update', $json_to_send)->getData();

    $leader_updated_db = Leader::query()->where('id', $leader->id)->first();
    $user_updated_db = User::query()->where('id', $leader->id)->first();

    $this->assertEquals($username_to_update, $user_updated_db->username);
    $this->assertEquals($gender_id_to_update, $user_updated_db->gender_id);
    $this->assertEquals($gender_interest_to_update, $leader_updated_db->interested_in_gender_id);
    $this->assertEquals($carousel_type_to_update, $leader_updated_db->carousel_type);

    $this->assertEquals($username_to_update, $response->username);
    $this->assertEquals($gender_id_to_update, $response->gender->id);
    $this->assertEquals($gender_interest_to_update, $response->type_attributes->interested_in_gender->id);
    $this->assertEquals($carousel_type_to_update, $response->type_attributes->carousel_type);


    //Get Rookies
    $response = $this->withHeaders($headers)->get($path . '/rookies/');
    $response->assertStatus(200);
    $response->assertJsonStructure($user_json_structure);


    //Rookie seen
    $rookies_ids = collect($response->getData()->data)->pluck('id')->toArray();
    $rookies_to_see = [
        'rookies_ids' => Arr::random($rookies_ids, 4)
    ];

    $response = $this->withHeaders($headers)->post($path . '/rookies/seen', $rookies_to_see);
    $response->assertStatus(200);

    $rookie_seen_history = RookieSeenHistory::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_see['rookies_ids'])
        ->get();
    $rookie_seen = RookieSeen::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_see['rookies_ids'])
        ->get();

    $this->assertEmpty($rookie_seen);
    $this->assertNotEmpty($rookie_seen_history);


    //Rookie save
    $rookies_to_save_count = 4;
    $rookies_to_save = Arr::random($rookies_ids, $rookies_to_save_count);

    foreach ($rookies_to_save as $rookie_to_save){
        $response = $this->withHeaders($headers)->post($path. '/rookies/' . $rookie_to_save . '/save');
        $response->assertStatus(201);
        $rookie_saved = RookieSaved::query()
            ->where('leader_id', $leader->id)
            ->where('rookie_id', $rookie_to_save)
            ->first();
        $this->assertNotNull($rookie_saved);
    }


    //Rookie unsave
    $rookies_to_unsave = $rookies_to_save;
    foreach ($rookies_to_unsave as $rookie_to_unsave) {
        $response = $this->withHeaders($headers)->delete($path . '/rookies/' . $rookie_to_unsave . '/save');
        $response->assertStatus(200);
    }

    $rookies_unsaved = RookieSaved::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_unsave)
        ->first();
    $this->assertNull($rookies_unsaved);


    //Open channel with rookie
    $rookie_open_channel = Arr::random($rookies_ids, 1)[0];
    $message = ['message' => 'testMessage'];

    $response = $this->withHeaders($headers)->post($path . '/rookies/' . $rookie_open_channel . '/channels', $message);
    $response->assertStatus(201);

    $channel = PubnubMessage::query()
        ->where('sender_id', $leader->id)
        ->where('receiver_id', $rookie_open_channel)
        ->first();
    $this->assertNotNull($channel);


    //Rookie carousel by path
    $path_id = Path::query()->where('is_subpath', false)->first()->id;

    $response = $this->withHeaders($headers)->get($path . '/rookies/?path_id=' . $path_id);
    $response->assertStatus(200);
    $this->assertNotEmpty($response->getData()->data);

    $rookie_path_id = $response->getData()->data[0]->type_attributes->path->id;
    $this->assertEquals($path_id, $rookie_path_id);


    //Rookie seen by carousel path
    $rookie_id = $response->getData()->data[0]->id;
    $rookie_to_see = [
        'rookies_ids' => [
            $rookie_id
        ]];

    $response = $this->withHeaders($headers)->post($path . '/rookies/seen', $rookie_to_see);
    $response->assertStatus(200);

    $rookie_seen_history = RookieSeenHistory::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_see['rookies_ids'])
        ->get();
    $rookie_seen = RookieSeen::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_see['rookies_ids'])
        ->get();

    $this->assertNotEmpty($rookie_seen_history);


    //Rookie carousel by sub path
    $subpath_id = Path::query()->where('is_subpath', true)->first()->id;
    $subpath_url = "?subpath_ids%5B0%5D={$subpath_id}";

    $response = $this->withHeaders($headers)->get($path . '/rookies/' . $subpath_url);
    $response->assertStatus(200);

    $this->assertNotEmpty($response->getData()->data);

    $rookie_subpath_id = $response->getData()->data[0]->type_attributes->subpath->id;

    $this->assertEquals($subpath_id, $rookie_subpath_id);


    //Rookie seen by carousel sub path
    $rookie_id = $response->getData()->data[0]->id;
    $rookie_to_see = [
        'rookies_ids' => [
            $rookie_id
        ]];

    $response = $this->withHeaders($headers)->post($path . '/rookies/seen', $rookie_to_see);
    $response->assertStatus(200);

    $rookie_seen_history = RookieSeenHistory::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_see['rookies_ids'])
        ->get();
    $rookie_seen = RookieSeen::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_see['rookies_ids'])
        ->get();

    $this->assertEmpty($rookie_seen);
    $this->assertNotEmpty($rookie_seen_history);


    //Rookie carousel by country
    $country_id = Country::first()->id;

    $response = $this->withHeaders($headers)->get($path . '/countries/' . $country_id . '/rookies');
    $response->assertStatus(200);

    $rookie_country_id = $response->getData()->data[0]->type_attributes->country->id;

    $this->assertEquals($country_id, $rookie_country_id);


    //Rookie seen by carousel country
    $rookie_id = $response->getData()->data[0]->id;
    $rookie_to_see = [
        'rookies_ids' => [
            $rookie_id
        ]];

    $response = $this->withHeaders($headers)->post($path . '/rookies/seen', $rookie_to_see);
    $response->assertStatus(200);

    $rookie_seen_history = RookieSeenHistory::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_see['rookies_ids'])
        ->get();
    $rookie_seen = RookieSeen::query()
        ->where('leader_id', $leader->id)
        ->whereIn('rookie_id', $rookies_to_see['rookies_ids'])
        ->get();

    $this->assertEmpty($rookie_seen);
    $this->assertNotEmpty($rookie_seen_history);
});
