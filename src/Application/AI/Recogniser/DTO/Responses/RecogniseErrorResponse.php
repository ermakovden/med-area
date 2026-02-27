<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\DTO\Responses;

use Shared\DTO\BaseDTO;

/**
 * Vision OCR API, REST: TextRecognitionAsync.Recognize
 *
 * Description: To send the image for asynchronous text recognition.
 *
 * See: https://yandex.cloud/ru/docs/vision/ocr/api-ref/TextRecognitionAsync/recognize#google.rpc.Status
 */
class RecogniseErrorResponse extends BaseDTO
{
    public int $code;

    public string $message;

    /** @var array<string, string> $details */
    public array $details;
}
