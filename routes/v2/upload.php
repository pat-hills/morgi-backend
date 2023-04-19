<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V2\UploadController;

Route::group(['middleware' => ['auth:api', 'untrusted', 'last_activity', 'active']], function () {
    Route::post('/upload', [UploadController::class, 'upload']);
    Route::post('/upload/multiple', [UploadController::class, 'multiUpload']);
});
