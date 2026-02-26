<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\DTO\Responses;

use Carbon\Carbon;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Optional;

/**
 * Vision OCR API, REST: TextRecognitionAsync.Recognize
 *
 * Description: To send the image for asynchronous text recognition.
 *
 * See: https://yandex.cloud/ru/docs/vision/ocr/api-ref/TextRecognitionAsync/recognize#yandex.cloud.operation.Operation
 */
class RecogniseAsyncResponse extends BaseDTO
{
    public string $id;

    public string $description;

    public Carbon $createdAt;

    public string $createdBy;

    public Carbon $modifiedAt;

    public bool $done;

    /**
     * @var array{
     *     textAnnotation: array{
     *         fullText: string,
     *         blocks: list<array<string, mixed>>
     *     }
     * }|null
     */
    public ?array $metadata;

    public RecogniseErrorResponse|Optional $status;

    /**
     * Get full text from textAnnotation
     *
     * @return string|null
     */
    public function getFullText(): ?string
    {
        /** @var string|null */
        return $this->metadata['textAnnotation']['fullText'] ?? null;
    }

    /**
     * Get blocks from textAnnotation
     *
     * @return list<array<string, mixed>>|null
     */
    public function getBlocks(): ?array
    {
        /** @var list<array<string, mixed>>|null */
        return $this->metadata['textAnnotation']['blocks'] ?? null;
    }
}
