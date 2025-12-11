<?php

declare(strict_types=1);

use Presentation\File\Controllers\FileController;

Route::prefix('files')->middleware(['auth'])->group(function () {
    Route::post('', [FileController::class, 'upload'])->name('api.files.upload');
});
