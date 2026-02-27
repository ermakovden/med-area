<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\DTO\Requests;

use Domain\AI\Recognise\Enums\YC\OCRModel;
use Shared\DTO\BaseDTO;
use Shared\Enums\LanguageCode;

/**
 * Vision OCR API, REST: TextRecognitionAsync.Recognize
 *
 * Description: To send the image for asynchronous text recognition.
 *
 * See: https://yandex.cloud/ru/docs/vision/ocr/api-ref/TextRecognitionAsync/recognize
 */
class RecogniseAsyncRequestDTO extends BaseDTO
{
    public string $content;

    public string $mimeType;

    /** @var array<LanguageCode> $languageCodes */
    public array $languageCodes;

    public OCRModel $model;
}
