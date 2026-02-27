<?php

declare(strict_types=1);

namespace Domain\AI\Recognise\Enums\YC;

/**
 * See: https://yandex.cloud/ru/docs/vision/concepts/ocr/template-recognition#models
 *
 * PAGE: https://yandex.cloud/ru/docs/vision/operations/ocr/text-detection-image
 * TABLE: https://yandex.cloud/ru/docs/vision/operations/ocr/text-detection-table
 * HANDWRITTEN: https://yandex.cloud/ru/docs/vision/operations/ocr/text-detection-handwritten
 */
enum OCRModel: string
{
    case PAGE = 'page'; // Images and PDF

    case TABLE = 'table'; // Excel and other

    case HANDWRITTEN = 'handwritten';

    case PASSPORT = 'passport';

    case DRIVER_LICENSE_FRONT = 'driver-license-front';

    case DRIVER_LICENSE_BACK = 'driver-license-back';

    case VEHICLE_REGISTRATION_FRONT = 'vehicle-registration-front';

    case VEHICLE_REGISTRATION_BACK = 'vehicle-registration-back';

    case LICENSE_PLATES = 'license-plates';
}
