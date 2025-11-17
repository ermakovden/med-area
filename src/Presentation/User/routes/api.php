<?php

declare(strict_types=1);

use Presentation\User\Controllers\UserController;

Route::post('/register', [UserController::class, 'register'])->name('api.users.register');
