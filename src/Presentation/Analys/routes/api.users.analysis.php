<?php

declare(strict_types=1);

use Presentation\Analys\Controllers\UserAnalysController;

Route::prefix('users/{userId}/analysis')->middleware(['auth'])->group(function () {
    Route::post('', [UserAnalysController::class, 'create'])->name('api.users.analysis.create');
});
