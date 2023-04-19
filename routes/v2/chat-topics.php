<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\ChatTopicController;
use Illuminate\Support\Facades\Route;
 
Route::post('/chat-topics', [ChatTopicController::class, 'store']); 
Route::get('/chat-topics', [ChatTopicController::class, 'index']); 
