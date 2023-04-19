<?php

use App\Models\Goal;
use App\Utils\User\UserTestUtils;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(DatabaseTransactions::class)->group('goal');

$payload = [
    'name' => 'Morgi Software License test',
    'details' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    'target_amount' => 2000,
    'start_date' => date('d-m-Y',time()),
    'end_date' =>  date('d-m-Y',strtotime('+9 day')),
    'thank_you_message'=> 'It has survived not only five centuries, but also the leap into electronic',
    'proof_note'=> 'an unknown printer took a galley of type and scrambled it to make a type specimen book.',
    'type_id' => 2,
    'has_image_proof' => true,
    'has_video_proof' => false,
   ];

test('rookie can get goal types list', function () {

    $rookie = $this->getRookie();
    $rookie->goals()->delete();
    $headers= [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken
    ];
    $response = $this->json(
        'GET',
        'api/goals/types',
        [], $headers);
    $goal_types = $response->getData();
    $response->assertOk();
    expect(count($goal_types))->toBeGreaterThan(0);
});

test('rookie can create goal', function () use ($payload) {
    $rookie = $this->getRookie();
    Storage::fake('local');
    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);
    $payload['featured_image'] = $fake_image;
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken,
        'Accept'=> 'application/json'
    ];
    $response = $this->post( 'api/goals', $payload, $headers);

    $data = $response->getData();
    $response->assertStatus(201);
    $goal = Goal::query()
        ->where('id', $data->id)
        ->first();

    $this->assertEquals($data->name, $payload['name']);
    $this->assertEquals($data->id, $goal->id);
});

test('rookie cannot create a goal whose date exceed its goal type constraints', function () use ($payload) {
    //delete existing goal
    $rookie = $this->getRookie();
    $rookie->goals()->delete();
    Storage::fake('local');
    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);
    $payload['featured_image'] = $fake_image;
    $payload['end_date'] =  date('d-m-Y',strtotime('+40 days'));
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken
    ];

    $response = $this->post( 'api/goals', $payload, $headers);

    $data = $response->getData();
    $response->assertStatus(400);
    expect($data)->toHaveProperty('message',"Days diffence must be in the range of goal type allowed period");
});



test('rookie cannot create a goal whoose target amount exceed its goal type constraints', function () use ($payload) {
    //delete existing goal
    $rookie = $this->getRookie();
    $rookie->goals()->delete();
    Storage::fake('local');
    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);
    $payload['featured_image'] = $fake_image;
    $payload['target_amount']= 1000000000;

    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken
    ];

    $response = $this->post( 'api/goals', $payload, $headers);

    $data = $response->getData();
    $response->assertStatus(400);
    expect($data)->toHaveProperty('message',"Amount not in range of goal type allowed amount");
});
