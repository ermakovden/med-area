<?php

declare(strict_types=1);

return [
    /**
     * Docs see here: https://yandex.cloud/ru/docs/vision/
     */
    'ocr' => [
        'key' => env('RECOGNISER_YC_KEY_ID'),
        'secret' => env('RECOGNISER_YC_SECRET_ACCESS_KEY'),
        'endpoint' => env('RECOGNISER_YC_URL'),
        'version' => env('RECOGNISER_YC_VERSION'),
    ],
];
