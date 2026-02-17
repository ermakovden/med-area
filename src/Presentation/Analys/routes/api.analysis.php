<?php

declare(strict_types=1);

use Presentation\Analys\Controllers\AnalysController;
use Presentation\Analys\Controllers\RecogniserController;

Route::prefix('analysis')->group(function () {
    Route::get('', [AnalysController::class, 'index'])->name('api.analysis.index');

    // Recogniser
    Route::prefix('recogniser')->group(function () {
        Route::post('', [RecogniserController::class, 'store'])->name('api.analysis.recogniser.create');
    });
});
