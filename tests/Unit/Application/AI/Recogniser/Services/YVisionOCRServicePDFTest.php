<?php

declare(strict_types=1);

namespace Tests\Unit\Application\AI\Recogniser\Services;

use Application\AI\Recogniser\DTO\Requests\RecogniseAsyncRequestDTO;
use Application\AI\Recogniser\DTO\Responses\RecogniseAsyncResponse;
use Application\AI\Recogniser\Services\YVisionOCRService;
use Domain\AI\Recognise\Enums\YC\OCRModel;
use Illuminate\Http\UploadedFile;
use Shared\Enums\LanguageCode;
use Tests\TestCase;

class YVisionOCRServicePDFTest extends TestCase
{
    /**
     * Сontains the service being tested
     *
     * @var YVisionOCRService
     */
    protected YVisionOCRService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new YVisionOCRService();
    }

    public function test_recognise_async_success_pdf(): void
    {
        // Testing pdf file for testing
        $file = UploadedFile::fake()->image('testing.pdf');

        // Data for testing
        $requestDTO = RecogniseAsyncRequestDTO::from([
            'content' => base64_encode($file->getContent()),
            'mimeType' => $file->extension(),
            'languageCodes' => [LanguageCode::RU],
            'model' => OCRModel::PAGE,
        ]);

        // Result from method of service
        $result = $this->service->recogniseAsync($requestDTO);

        // Check asserts that success
        $this->assertInstanceOf(RecogniseAsyncResponse::class, $result);
        $this->assertFalse($result->done);
    }
}
