<?php

declare(strict_types=1);

use Presentation\User\Controllers\RegistrationController;

Route::post('/register', [RegistrationController::class, 'register'])->name('api.users.register');
