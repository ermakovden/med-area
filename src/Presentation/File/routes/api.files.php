<?php

declare(strict_types=1);

use Presentation\File\Controllers\FileController;

Route::prefix('files')->middleware(['auth'])->group(function () {
    Route::post('', [FileController::class, 'upload'])->name('api.files.upload');
    Route::get('', [FileController::class, 'index'])->name('api.files.index');
    Route::delete('', [FileController::class, 'destroy'])->name('api.files.destroy');
});
