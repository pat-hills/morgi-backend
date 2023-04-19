<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\RookieCarouselController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'countries'], function () {
    Route::get('/', [CountryController::class, 'index']);
    Route::get('/localize', [CountryController::class, 'localize']);
    Route::get('/{country}/rookies', [RookieCarouselController::class, 'getRookiesByCountry']);
});
