<?php

use App\Models\Leader;
use App\Models\Path;
use App\Models\PubnubChannel;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\User;
use Database\Factories\tests\LeaderTestFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

$path = '/v2';
$micro_morgi_to_send = 1;
$subscription_package_id = 1;
$micro_morgi_package = 1;
$email = Str::random(10) . 'test@gmail.com';
$password = '12345678';

test('Payments Flow', function () use ($path, $micro_morgi_to_send, $subscription_package_id, $micro_morgi_package, $email, $password) {
    //Sign Up
    $leader = LeaderTestFactory::first();
    $leader['email'] = $email;
    $leader['password'] = $password;
    $leader['password_confirmation'] = $password;

    $leader_path = Path::query()->where('is_subpath', false)->first();
    $leader['path_id'] = $leader_path->id;

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


    //GetRookies Test
    $access_token = $response->getData()->access_token;
    $headers = [
        'Accept' => 'application/json',
        'Authorization' => "Bearer $access_token"
    ];

    $response = $this->withHeaders($headers)->get($path . '/rookies');
    $response->assertStatus(200);

    $first_rookie = $response->getData()->data[0];
    $this->assertNotEmpty($first_rookie);


    //Open channel Test
    $data = [
        'message' => "Hi, i'm leader."
    ];
    $response = $this->withHeaders($headers)->post($path . "/rookies/{$first_rookie->id}/channels", $data);
    $response->assertStatus(201);

    $channel = PubnubChannel::query()
        ->where('leader_id', $leader->id)
        ->where('rookie_id', $first_rookie->id)
        ->first();
    $this->assertNotNull($channel);


    //Subscribe Rookie test
    $data = [
        'subscription_package_id' => $subscription_package_id
    ];
    //Fake redirect to form
    $response = $this->withHeaders($headers)->post($path . "/rookies/{$first_rookie->id}/subscriptions/old", $data);
    $response->assertStatus(303);
    //Subscription Old
    $response = $this->withHeaders($headers)->post($path . "/rookies/{$first_rookie->id}/subscriptions/old", $data);
    $response->assertStatus(200);

    $subscription = Subscription::query()
        ->where('leader_id', $leader->id)
        ->where('rookie_id', $first_rookie->id)
        ->first();
    $this->assertNotNull($subscription);


    //Buy micromorgi Test
    $response = $this->withHeaders($headers)->post($path . "/micromorgi-packages/{$micro_morgi_package}/buy");
    $response->assertStatus(200);

    $micro_morgi_balance = Leader::query()
        ->where('id', $leader->id)
        ->first()
        ->micro_morgi_balance;
    expect($micro_morgi_balance)->toBeGreaterThan(0);


    //Send micromorgi to Rookie Test
    $old_micro_morgi_balance = Rookie::query()
        ->where('id', $first_rookie->id)
        ->first()
        ->micro_morgi_balance;

    $response = $this->withHeaders($headers)->post($path . "/rookies/{$first_rookie->id}/micromorgi/{$micro_morgi_to_send}");
    $response->assertStatus(200);

    $new_micro_morgi_balance = Rookie::query()
        ->where('id', $first_rookie->id)
        ->first()
        ->micro_morgi_balance;

    expect($new_micro_morgi_balance)->toBeGreaterThan($old_micro_morgi_balance);
});


