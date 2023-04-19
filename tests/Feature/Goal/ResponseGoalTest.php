<?php

namespace tests\Feature\Goal;

use App\Models\Goal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class)->group('goal', 'response-goal');

$payload = [
    'name' => 'Morgi Software License test',
    'details' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    'target_amount' => 2000,
    'start_date' => date('d-m-Y',time()),
    'end_date' =>  date('d-m-Y',strtotime('+9 day')),
    'thank_you_message'=> 'test has survived not only five centuries, but also the leap into electronic',
    'has_image_proof' => true,
    'has_video_proof' => false,
    'proof_note'=> 'an unknown printer took a galley of type and scrambled it to make a type specimen book.',
    'type_id' => 2,
];

test('Goal creation returns the correct data structure', function () use ($payload) {
    //delete existing goal
    $rookie = $this->getRookie();
    $rookie->goals()->delete();
    Storage::fake('local');
    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);
    $payload['featured_image'] = $fake_image;

    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken,
        'Accept'=> 'application/json'
    ];
    $response = $this->post( 'v2/goals', $payload, $headers);
    $data = $response->getData();

    $response->assertStatus(201);
    expect($data)->toHaveProperties([
        "id", "name", "slug", "details", "target_amount",
        "currency_type", "rookie_id", "start_date", "end_date", "thank_you_message",
        "cancelled_at", "cancelled_reason", "type_id", "status", "proof_note",
        "has_image_proof", "has_video_proof", "type", "media"
    ]);

    expect($payload['name'])->toBe($data->name);
});

test('Goal update returns the correct structure', function () use ($payload) {
    //delete existing goal
    $rookie = $this->getRookie();
    $goal = Goal::create(array_merge($payload,[
        'rookie_id' => $rookie->id,
        'slug' => Str::random(),
        'status' => Goal::STATUS_ACTIVE,
    ]));

    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken,
        'Accept'=> 'application/json'
    ];
    $response = $this->patch( "v2/goals/$goal->id", $payload, $headers);
    $data = $response->getData();

    $response->assertStatus(200);
    expect($data)->toHaveProperties([
        "id", "name", "slug", "details", "target_amount",
        "currency_type", "rookie_id", "start_date", "end_date", "thank_you_message",
        "cancelled_at", "cancelled_reason", "type_id", "status", "proof_note",
        "has_image_proof", "has_video_proof", "type", "media"
    ]);
});


test('Goal index returns the correct structure', function () use ($payload) {
    //delete existing goal
    $rookie = $this->getRookie();

    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken,
        'Accept'=> 'application/json'
    ];
    $response = $this->get( "v2/rookies/$rookie->id/goals", $headers);
    $data = $response->getData();

    $response->assertStatus(200);
    foreach ($data as $item) {
        expect($item)->toHaveProperties([
            "id", "name", "slug", "details", "target_amount",
            "currency_type", "rookie_id", "start_date", "end_date", "thank_you_message",
            "cancelled_at", "cancelled_reason", "type_id", "status", "proof_note",
            "created_at", "updated_at", "has_image_proof", "has_video_proof", "type",
            "media"
        ]);
    }
});
