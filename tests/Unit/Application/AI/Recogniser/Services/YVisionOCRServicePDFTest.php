<?php

declare(strict_types=1);

namespace Tests\Unit\Application\AI\Recogniser\Services;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;
use Application\AI\Recogniser\DTO\Requests\RecogniseAsyncRequestDTO;
use Application\AI\Recogniser\Services\Contracts\RecogniseRequestServiceContract;
use Application\AI\Recogniser\Services\YVisionOCRService;
use Domain\AI\Recognise\Enums\RecogniseStatus;
use Domain\AI\Recognise\Enums\YC\OCRModel;
use Domain\AI\Recognise\Factories\RecogniseRequestFactory;
use Domain\AI\Recognise\Models\RecogniseRequest;
use Domain\File\Factories\FileFactory;
use Domain\File\Models\File;
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

        $this->service = new YVisionOCRService(
            app(RecogniseRequestServiceContract::class),
        );
    }

    public function test_recognise_async_success_pdf(): void
    {
        $this->test_recognise_async_success('pdf');
    }

    public function test_recognise_async_success_jpeg(): void
    {
        $this->test_recognise_async_success('jpeg');
    }

    public function test_recognise_async_success_jpg(): void
    {
        $this->test_recognise_async_success('jpg');
    }

    public function test_recognise_async_success_png(): void
    {
        $this->test_recognise_async_success('png');
    }


    protected function test_recognise_async_success(string $extension): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Testing pdf file for testing
        $file = UploadedFile::fake()->image('testing.' . $extension);

        // Data for testing
        $fileFactory = new FileFactory();
        $recogniseRequestFactory = new RecogniseRequestFactory();

        $fileModel = File::create(array_merge($fileFactory->definition(), ['user_id' => $user->id]));

        $recogniseRequetModel = RecogniseRequest::create(
            array_merge($recogniseRequestFactory->definition(), [
                'user_id' => $user->id,
                'file_id' => $fileModel->id,
            ])
        );
        $recogniseRequestDTO = RecogniseRequestDTO::from($recogniseRequetModel);

        $requestDTO = RecogniseAsyncRequestDTO::from([
            'content' => base64_encode($file->getContent()),
            'mimeType' => $file->extension(),
            'languageCodes' => [LanguageCode::RU],
            'model' => OCRModel::PAGE,
        ]);

        // Result from method of service
        $result = $this->service->recogniseAsync($requestDTO, $recogniseRequestDTO);

        // Check asserts that success
        $this->assertInstanceOf(RecogniseRequestDTO::class, $result);
        $this->assertIsString($result->operation_id);
        $this->assertSame(RecogniseStatus::PROCESSED, $result->status);
    }
}
