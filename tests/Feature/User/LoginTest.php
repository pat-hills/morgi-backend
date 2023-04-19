<?php

use Illuminate\Http\Request;

test('User login', function (){
    $user = \App\Models\User::where('active', true)->whereIn('type', ['rookie', 'leader'])->inRandomOrder()->first();
    $request = new Request();

    if ($request->header('User-Agent') === null) {
        $request->headers->set('User-Agent', '');
    }

    $login = \App\Utils\User\Auth\AuthUtils::login($request, $user, 'morgi');

    expect($login)->toBeArray();
});
