<?php

use Database\Factories\tests\LeaderTestFactory;
use Database\Factories\tests\RookieTestFactory;
use Illuminate\Http\UploadedFile;

test('Leader signup', function (){
    $user_attributes = LeaderTestFactory::first();
    $response = $this->postJson("api/auth/signup", $user_attributes);
    $response->assertStatus(201);
    $user = $response->json();
});
