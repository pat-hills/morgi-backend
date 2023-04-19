<?php

use App\Models\Goal;
use App\Models\GoalType;
use App\Models\Path;
use App\Models\UserPath;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

test('leader can get path with active goals', function () use ($payload) {

    $rookie = $this->getRookie();
    Goal::create(
        array_merge($payload, [
            'rookie_id' => $rookie->id,
            'slug' => \Illuminate\Support\Str::random(),
            'status' => Goal::STATUS_ACTIVE
        ])
    );

    $leader = $this->getLeader();
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $response = $this->json('GET', 'v2/goals/paths', [], $headers);
    $paths = $response->getData();
    $response->assertOk();
    expect(count($paths->data))->toBeGreaterThan(0);
});

test('leader cannot get paths with non existing path name', function () use ($payload) {
    $rookie = $this->getRookie();

    Goal::create(
        array_merge($payload, [
            'rookie_id' => $rookie->id,
            'slug' => \Illuminate\Support\Str::random(),
            'status' => Goal::STATUS_ACTIVE,
        ])
    );

    $leader = $this->getLeader();
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $response = $this->json('GET', 'api/goals/paths?name=' . Str::random(), [], $headers);
    $paths = $response->getData();
    $response->assertOk();
    expect(count($paths->data))->toBe(0);
});

test('leader can get paths(with active goals) with valid path name', function () use ($payload) {

    $rookie = $this->getRookie();

    Goal::create(
        array_merge($payload, [
            'rookie_id' => $rookie->id,
            'slug' => \Illuminate\Support\Str::random(),
            'status' => Goal::STATUS_ACTIVE,
        ])
    );

    $leader = $this->getLeader();
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $path = UserPath::query()->where('user_id', $rookie->id)->whereNull('deleted_at')->first();
    $path_name = Path::query()->where('id', $path->path_id)->first()->key_name;
    $response = $this->json('GET', 'api/goals/paths?name=' . $path_name, [], $headers);
    $paths = $response->getData();
    $response->assertOk();
    expect(count($paths->data))->toBe(1);
});

test('leader can get path with active goals filter by type id', function () use ($payload) {

    $rookie = $this->getRookie();
    $goal_type = GoalType::create([
        'type' => 'Premium',
        'min' => 500,
        'max' => 20000,
        'duration_value' => 5
    ]);
    Goal::create(
        array_merge($payload, [
            'rookie_id' => $rookie->id,
            'slug' => Str::random(),
            'status' => Goal::STATUS_ACTIVE,
            'type_id' => $goal_type->id
        ])
    );
    $leader = $this->getLeader();
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $response = $this->json('GET', 'api/goals/paths?type_id='.$goal_type->id, [], $headers);
    $paths = $response->getData();
    $response->assertOk();
    expect(count($paths->data))->toBe(1);
});
