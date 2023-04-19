<?php


use App\Models\Goal;
use App\Models\GoalDonation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;

uses(DatabaseTransactions::class);

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

it('Leader can get goals order by 24hr remaining', function () use ($payload) {
    $rookie = $this->getRookie();
    $leader = $this->getLeader();
    // to be sure, delete all existing goal
    Goal::truncate();
    $times = [ '+4 hour', '+4 hour', '+2 day'];
    // create mutiple test goals
    for ($i=0; $i < count($times); $i++) {
        Goal::create(
            array_merge($payload, [
                'slug' => "testgoal{$i}",
                'end_date' =>  date('d-m-Y H:i:s', strtotime($times[$i])),
                'rookie_id' => $rookie->id,
                'status' =>Goal::STATUS_ACTIVE,
            ])
        );
    }

    $headers =  [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $response = $this->json('GET', 'v2/goals?time_range=24', [], $headers);
    $goals = $response->getData();
    $response->assertOk();
    expect(count($goals->data))->toBe(2);
});

it('Leader can get goals order by amount in ASC order', function () use ($payload) {
    $rookie = $this->getRookie();
    $leader = $this->getLeader();
    // to be sure, delete all existing goal
    Goal::truncate();
    // create mutiple test goals
    for ($i=0; $i < 3; $i++) {
        Goal::create(
            array_merge($payload, [
                'slug' => "testgoal{$i}",
                'end_date' =>  Carbon::now()->addDays(7),
                'rookie_id' => $rookie->id,
                'target_amount' => 500 * ($i+1),
                'status' =>Goal::STATUS_ACTIVE,
            ])
        );
    }
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $response = $this->json('GET', 'v2/goals?order_by=target_amount&order_direction=asc', [], $headers);
    $goals = $response->getData();
    $response->assertOk();
    expect($goals->data[0]->target_amount)->toBeLessThan($goals->data[1]->target_amount);
    expect($goals->data[1]->target_amount)->toBeLessThan($goals->data[2]->target_amount);
});

it('Leader can get goals order by amount in DESC order', function () use ($payload) {
    $rookie = $this->getRookie();
    $leader = $this->getLeader();
    // to be sure, delete all existing goal
    Goal::truncate();
    // create mutiple test goals
    for ($i=0; $i < 3; $i++) {
        Goal::create(
            array_merge($payload, [
                'slug' => "testgoal{$i}",
                'end_date' =>  Carbon::now()->addDays(7),
                'rookie_id' => $rookie->id,
                'target_amount' => 500 * ($i+1),
                'status' =>Goal::STATUS_ACTIVE,
            ])
        );
    }
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $response = $this->json('GET', 'v2/goals?order_by=target_amount&order_direction=desc', [], $headers);
    $goals = $response->getData();
    $response->assertOk();
    expect($goals->data[0]->target_amount)->toBeGreaterThan($goals->data[1]->target_amount);
    expect($goals->data[1]->target_amount)->toBeGreaterThan($goals->data[2]->target_amount);
});


it('Leader can get goals order by popularity-default', function () use ($payload) {
    $rookie = $this->getRookie();
    $leader = $this->getLeader();
    // to be sure, delete all existing goal
    Goal::truncate();
    GoalDonation::truncate();
    // create mutiple test goals
    $popular_goal = null;
    for ($i=0; $i < 3; $i++) {
        $goal = Goal::create(
            array_merge($payload, [
                'slug' => "testgoal{$i}",
                'end_date' => Carbon::now()->addDays(7),
                'rookie_id' => $rookie->id,
                'target_amount' => 500 * ($i+1),
                'status' =>Goal::STATUS_ACTIVE,
            ])
        );
        if ($i === 2){
            $popular_goal = $goal;
            GoalDonation::create([
                'amount' => 500,
                'leader_id' =>  $leader->id,
                'goal_id' => $goal->id
            ]);
        }
    }
    $headers = [
        'HTTP_Authorization' => 'Bearer ' . $leader->accessToken
    ];
    $response = $this->json('GET', 'v2/goals', [], $headers);
    $goals = $response->getData();

    $response->assertOk();
    expect($goals->data[0]->id)->toBe($popular_goal->id);
});
