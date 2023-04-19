<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;

Route::group(['prefix' => 'password-recovery'], function () {
    Route::post('/', [PasswordResetController::class, 'create']);
    Route::get('/{token}', [PasswordResetController::class, 'find']);
    Route::post('/reset', [PasswordResetController::class, 'reset'])->name('password.reset');
});
