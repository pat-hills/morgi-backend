<?php

use App\Models\Goal;
use App\Models\GoalDonation;
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
    'target_amount' => 500,
    'start_date' => date('d-m-Y',time()),
    'end_date' =>  date('d-m-Y',strtotime('+9 day')),
    'thank_you_message'=> 'It has survived not only five centuries, but also the leap into electronic',
    'proof_note'=> 'an unknown printer took a galley of type and scrambled it to make a type specimen book.',
    'type_id' => 2,
    'has_image_proof' => true,
    'has_video_proof' => false,
   ];

test('rookie can get goal supporters', function () use ($payload) {
    $rookie = $this->getRookie();
    $goal = Goal::create(
        array_merge($payload, [
            'rookie_id' => $rookie->id,
            'slug' => Str::random(),
            'status' => Goal::STATUS_ACTIVE
        ])
    );
    $leader = $this->getLeader();
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken
    ];
    GoalDonation::create(['amount'=> 500, 'leader_id' => $leader->id, 'goal_id' => $goal->id]);
    $response = $this->json('GET', 'api/goals/'.$goal->id.'/supporters', [], $headers);
    $supporters = $response->getData();
    $response->assertOk();
    expect(count($supporters))->toBeGreaterThan(0);
});
