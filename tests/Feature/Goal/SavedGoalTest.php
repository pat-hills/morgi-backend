<?php


use App\Models\Goal;
use App\Models\SavedGoal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(DatabaseTransactions::class);

$goal_data = [
    'name' => 'Morgi Software License test',
    'details' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
    'target_amount' => 500,
    'start_date' => date('d-m-Y',time()),
    'end_date' =>  date('d-m-Y',strtotime('+9 day')),
    'thank_you_message'=> 'It has survived not only five centuries, but also the leap into electronic',
    'proof_note'=> 'an unknown printer took a galley of type and scrambled it to make a type specimen book.',
    'type_id' => 2,
    'proof_type' => json_encode(['image']),
   ];


it('leader can save goal', function () use ($goal_data) {
    Goal::truncate();
    $rookie = $this->getRookie();
    $leader = $this->getLeader();
    Storage::fake('local');

    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);

    $goal_data['featured_image'] = $fake_image;
    $goal_data['rookie_id'] = $rookie->id;
    $goal = Goal::create($goal_data);

    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken,
        'Accept'=> 'application/json'
    ];
    $response = $this->post( 'v2/goals/'.$goal->id.'/save', [], $headers);
    $response->assertStatus(200);

    $is_goal_saved = SavedGoal::query()
        ->where('goal_id', $goal->id)
        ->where('leader_id', $leader->id)
        ->exists();
    $this->assertTrue($is_goal_saved);
});

it('leader can unsave goal', function () use ($goal_data) {
    Goal::truncate();
    $rookie = $this->getRookie();
    $leader = $this->getLeader();
    Storage::fake('local');

    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);

    $goal_data['featured_image'] = $fake_image;
    $goal_data['rookie_id'] = $rookie->id;
    $goal = Goal::create($goal_data);

    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken,
        'Accept'=> 'application/json'
    ];
    $response = $this->delete( 'v2/goals/'.$goal->id.'/save',[], $headers);
    $response->assertStatus(200);

    $is_goal_saved = SavedGoal::query()
        ->where('goal_id', $goal->id)
        ->where('leader_id', $leader->id)
        ->exists();
    $this->assertFalse($is_goal_saved);
});

it('leader can  get saved goal', function () use ($goal_data) {
    Goal::truncate();
    $rookie = $this->getRookie();
    $leader = $this->getLeader();
    Storage::fake('local');

    $fake_image = UploadedFile::fake()->image('avatar.jpg', 400, 400)->size(100);

    $goal_data['featured_image'] = $fake_image;
    $goal_data['rookie_id'] = $rookie->id;
    $goal = Goal::create($goal_data);
    SavedGoal::create([
        'leader_id' => $leader->id,
        'goal_id' => $goal->id,
    ]);

    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $response = $this->json( 'GET', 'v2/goals/saved', [], $headers);

    $data = $response->getData()->data;
    $response->assertStatus(200);
    expect(count($data))->toBe(1);
});
