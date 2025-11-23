<?php

declare(strict_types=1);

use Presentation\User\Controllers\UserController;

Route::prefix('users')->middleware(['auth'])->group(function () {
    Route::get('/me', [UserController::class, 'me'])->name('api.users.me');
});
