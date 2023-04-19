<?php

use App\Models\Goal;
use App\Models\PubnubChannel;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

test('Leader payment goal', function () use ($payload) {

    $rookie = $this->getRookie();
    $goal = Goal::create(
        array_merge($payload, [
            'rookie_id' => $rookie->id,
            'slug' => Str::random(),
            'status' => Goal::STATUS_ACTIVE,
        ])
    );

    $leader = $this->getLeader();
    $headers = [
        'Authorization' => 'Bearer ' . $leader->accessToken,
        'Accept'=> 'application/json'
    ];

    $micro_morgi_package = 1;
    //Response 303 because FAKE_CCBILL_ACTIVE = true
    $this->post("/v2/micromorgi-packages/{$micro_morgi_package}/buy", [], $headers);
    $response = $this->post("/v2/micromorgi-packages/{$micro_morgi_package}/buy", [], $headers);
    $response->assertStatus(200);

    $goal_payload = ['amount' => 1];
    $response = $this->withHeaders($headers)->post("/v2/rookies/{$rookie->id}/goals/{$goal->id}/donate", $goal_payload);
    $response->assertStatus(200);

    $transaction = Transaction::query()
        ->where('rookie_id', $rookie->id)
        ->where('leader_id', $leader->id)
        ->where('type', 'goal')
        ->first();
    $this->assertNotNull($transaction);

    $pubnub_channel = PubnubChannel::query()
        ->where('rookie_id', $rookie->id)
        ->where('leader_id', $leader->id)
        ->where('goal_id', $goal->id)
        ->get();
    $this->assertNotNull($pubnub_channel);
});
