<?php

declare(strict_types=1);

use Presentation\Analys\Controllers\UserAnalysController;

Route::prefix('users/{userId}/analysis')->middleware(['auth'])->group(function () {
    Route::post('', [UserAnalysController::class, 'create'])->name('api.users.analysis.create');
    Route::get('', [UserAnalysController::class, 'index'])->name('api.users.analysis.index');
    Route::delete('', [UserAnalysController::class, 'destroy'])->name('api.users.analysis.destroy');
});
