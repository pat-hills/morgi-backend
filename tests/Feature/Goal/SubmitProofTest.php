<?php

use App\Models\Goal;
use App\Models\GoalDonation;
use App\Models\GoalProof;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

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

it('can submit goal proof successfuly', function () use ($payload) {

    $rookie = $this->getRookie();
    $goal = Goal::create(array_merge($payload,[
         'rookie_id' => $rookie->id,
         'status' => Goal::STATUS_AWAITING_PROOF,
         'slug' => Str::random()
    ]));
    GoalDonation::create([
        'amount'=> $goal->target_amount,
        'leader_id'=> 1,
        'goal_id'=> $goal->id
    ]);
     $response = $this->json('POST','v2/goals/'.$goal->id.'/proofs', [
        'message' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        'proofs' => [[
                'type' => 'image',
                'url' => 'fake_url'
            ]]
     ], [
        'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken
        ,
        'Accept'=> 'application/json'
     ]);
    $response->assertStatus(201);
    $is_created_proof = GoalProof::query()->where('goal_id', $goal->id)->exists();
    $this->assertTrue($is_created_proof);

})->group('proof');

it('cannot submit goal that hasn\'t reach 100% or has end but not reach 75%', function () use ($payload) {
    $rookie = $this->getRookie();
    $goal = Goal::create(array_merge($payload,[
        'rookie_id' => $rookie->id,
        'slug' => Str::random(),
        'status' => Goal::STATUS_AWAITING_PROOF,
    ]));
    GoalDonation::truncate();
    $rookie = $this->getRookie($goal->rookie_id);
    Storage::fake('local');
    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);

    $response = $this->json('POST','v2/goals/'.$goal->id.'/proofs', [
       'message' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
       'proof_image' => $fake_image,

    ], [
       'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken,
       'Accept'=> 'application/json'
    ]);

   $response->assertStatus(400);
})->group('proof');


it('cannot submit goal without proof message', function () use ($payload) {
    $rookie = $this->getRookie();
    $goal = Goal::create(array_merge($payload,[
        'rookie_id' => $rookie->id,
        'slug' => Str::random(),
        'status' => Goal::STATUS_AWAITING_PROOF,
    ]));
    //donate target amount
    GoalDonation::create([
        'amount'=> $goal->target_amount,
        'leader_id'=> 1,
        'goal_id'=> $goal->id
     ]);
    $rookie = $this->getRookie($goal->rookie_id);
    Storage::fake('local');
    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);

    $response = $this->json('POST','v2/goals/'.$goal->id.'/proofs', [
       'proof_image' => $fake_image,

    ], [
       'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken
       ,
       'Accept'=> 'application/json'
    ]);

   $response->assertStatus(400);
})->group('proof');


it('cannot submit goal without either video or image', function () use ($payload) {
    $rookie = $this->getRookie();
    $goal = Goal::create(array_merge($payload,[
        'rookie_id' => $rookie->id,
        'slug' => Str::random(),
        'status' => Goal::STATUS_AWAITING_PROOF,
    ]));
    //donate target amount
    GoalDonation::create([
        'amount'=> $goal->target_amount,
        'leader_id'=> 1,
        'goal_id'=> $goal->id
     ]);
    $rookie = $this->getRookie($goal->rookie_id);
    Storage::fake('local');

    $response = $this->json('POST','v2/goals/'.$goal->id.'/proofs', [
        'message' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    ], [
       'HTTP_Authorization' => 'Bearer ' . $rookie->accessToken
       ,
       'Accept'=> 'application/json'
    ]);

   $response->assertStatus(400);
})->group('proof');
