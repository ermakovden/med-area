<?php

declare(strict_types=1);

use Presentation\Analys\Controllers\AnalysController;

Route::prefix('analysis')->group(function () {
    Route::get('', [AnalysController::class, 'index'])->name('api.analysis.index');
});
