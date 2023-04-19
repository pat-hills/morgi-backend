<?php

test("Test getRookieUser() and getLeaderUser()", function () {

    $rookie_user = \App\Models\User::where('type', 'rookie')
        ->where('active', true)
        ->inRandomOrder()
        ->first();
    if(!isset($rookie_user)){
        return;
    }

    $leader_user = \App\Models\User::where('type', 'leader')
        ->where('active', true)
        ->inRandomOrder()
        ->first();
    if(!isset($leader_user)){
        return;
    }

    $second_rookie_user = \App\Models\User::where('type', 'rookie')
        ->where('active', true)
        ->where('id', '!=', $rookie_user->id)
        ->inRandomOrder()
        ->first();
    if(!isset($second_rookie_user)){
        return;
    }

    $second_leader_user = \App\Models\User::where('type', 'leader')
        ->where('active', true)
        ->where('id', '!=', $leader_user->id)
        ->inRandomOrder()
        ->first();
    if(!isset($second_leader_user)){
        return;
    }

    $rookie = \App\Utils\UserBlockUtils::getRookieUser($rookie_user, $leader_user);
    expect($rookie)->not->toBeNull()
        ->id->toBe($rookie_user->id);

    $leader = \App\Utils\UserBlockUtils::getLeaderUser($second_leader_user, $second_rookie_user);
    expect($leader)->not->toBeNull()
        ->id->toBe($second_leader_user->id);
});

test("Test rookie block/unblock leader", function () {

    $rookie_user = \App\Models\User::where('type', 'rookie')
        ->where('active', true)
        ->inRandomOrder()
        ->first();
    if(!isset($rookie_user)){
        return;
    }

    $leaders_blocked_ids = \App\Models\UserBlock::where('from_user_id', $rookie_user->id)
        ->whereNull('deleted_at')
        ->pluck('to_user_id')
        ->toArray();

    $non_blocked_leader_user = \App\Models\User::where('type', 'leader')
        ->where('active', true)
        ->whereNotIn('id', $leaders_blocked_ids)
        ->inRandomOrder()
        ->first();
    if(isset($non_blocked_leader_user)){
        $response = $this->actingAs($rookie_user, 'api')
            ->postJson("api/leader/{$non_blocked_leader_user->id}/block");
        $response->assertStatus(200);
    }

    $leaders_blocked_ids = \App\Models\UserBlock::where('from_user_id', $rookie_user->id)
        ->whereNull('deleted_at')
        ->pluck('to_user_id')
        ->toArray();

    $blocked_leader_user = \App\Models\User::where('type', 'leader')
        ->where('active', true)
        ->whereIn('id', $leaders_blocked_ids)
        ->inRandomOrder()
        ->first();
    if(isset($blocked_leader_user)){
        $response = $this->actingAs($rookie_user, 'api')
            ->postJson("api/leader/{$blocked_leader_user->id}/block");
        $response->assertStatus(400);

        $response = $this->actingAs($rookie_user, 'api')
            ->deleteJson("api/leader/{$blocked_leader_user->id}/block");
        $response->assertStatus(204);
    }
});

test("Test leader block/unblock rookie", function () {

    $leader_user = \App\Models\User::where('type', 'leader')
        ->where('active', true)
        ->inRandomOrder()
        ->first();
    if(!isset($leader_user)){
        return;
    }

    $rookies_blocked_ids = \App\Models\UserBlock::where('from_user_id', $leader_user->id)
        ->whereNull('deleted_at')
        ->pluck('to_user_id')
        ->toArray();

    $non_blocked_rookie_user = \App\Models\User::where('type', 'rookie')
        ->where('active', true)
        ->whereNotIn('id', $rookies_blocked_ids)
        ->inRandomOrder()
        ->first();
    if(isset($non_blocked_rookie_user)){
        $response = $this->actingAs($leader_user, 'api')
            ->postJson("api/rookie/{$non_blocked_rookie_user->id}/block");
        $response->assertStatus(200);
    }

    $rookies_blocked_ids = \App\Models\UserBlock::where('from_user_id', $leader_user->id)
        ->whereNull('deleted_at')
        ->pluck('to_user_id')
        ->toArray();

    $blocked_rookie_user = \App\Models\User::where('type', 'rookie')
        ->where('active', true)
        ->whereIn('id', $rookies_blocked_ids)
        ->inRandomOrder()
        ->first();
    if(isset($blocked_rookie_user)){
        $response = $this->actingAs($leader_user, 'api')
            ->postJson("api/rookie/{$blocked_rookie_user->id}/block");
        $response->assertStatus(400);

        $response = $this->actingAs($leader_user, 'api')
            ->deleteJson("api/rookie/{$blocked_rookie_user->id}/block");
        $response->assertStatus(204);
    }
});
