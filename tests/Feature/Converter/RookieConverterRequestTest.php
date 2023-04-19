<?php

use App\Models\Rookie;
use App\Models\RookiesConverterRequest;

it('can submit rookie converter request successfuly', function () {

    $user_rookie = $this->getRookie();
    $rookie = Rookie::query()->where('id', $user_rookie->id)->first();

    $rookie->convertersRequest()->delete();
    $response = $this->json('POST','api/profile/converters', [
    'message' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    ], [
    'HTTP_Authorization' => 'Bearer ' . $user_rookie->accessToken
    ,
    'Accept'=> 'application/json'
    ]);
    $data = $response->getData();
    $response->assertStatus(201);
})->group('converters');

it('can edit rookie converter request successfuly', function () {

    $user_rookie = $this->getRookie();
    $rookie = Rookie::query()->where('id', $user_rookie->id)->first();

    $rookie->convertersRequest()->delete();
    $converter = RookiesConverterRequest::create([
        'rookie_id' => $user_rookie->id,
        'message' => 'random random stuff'
    ]);
    $response = $this->json('PATCH','api/profile/converters/'.$converter->id, [
    'message' => 'Lorem.',
    ], [
    'HTTP_Authorization' => 'Bearer ' . $user_rookie->accessToken
    ,
    'Accept'=> 'application/json'
    ]);
    $converter->refresh();
    $data = $response->getData();
    $response->assertStatus(200);
    expect($data)->toHaveProperty('message');
    $this->assertNotEquals($converter->message, 'random random stuff');
})->group('converters');

it('can only allow one converter request', function () {

    $user_rookie = $this->getRookie();
    $rookie = Rookie::query()->where('id', $user_rookie->id)->first();

    $rookie->convertersRequest()->delete();
    $converter = RookiesConverterRequest::create([
        'rookie_id' => $user_rookie->id,
        'message' => 'random random stuff'
    ]);
    $response = $this->json('POST','api/profile/converters', [
        'message' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        ], [
        'HTTP_Authorization' => 'Bearer ' . $user_rookie->accessToken
        ,
        'Accept'=> 'application/json'
        ]);
    $converter->refresh();
    $response->assertStatus(400);
})->group('converters');
