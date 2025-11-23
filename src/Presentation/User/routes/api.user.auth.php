<?php

declare(strict_types=1);

use Presentation\User\Controllers\AuthController;

Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware(['throttle:6,1'])
        ->name('api.auth.login');
});
