<?php

use App\Http\Controllers\AppInfoController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'app-info'], function () {
    Route::get('version', [AppInfoController::class, 'version']);
});

