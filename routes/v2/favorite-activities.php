<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\FavoriteActivityController;
use Illuminate\Support\Facades\Route;
 
Route::post('/favorite-activities', [FavoriteActivityController::class, 'store']);
Route::get('/favorite-activities', [FavoriteActivityController::class, 'index']); 