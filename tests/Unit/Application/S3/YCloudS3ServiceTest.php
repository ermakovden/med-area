<?php

declare(strict_types=1);

namespace Tests\Unit\Application\S3;

use Application\S3\DTO\FileDTO;
use Application\S3\Services\YCloudS3Service;
use Domain\File\Factories\FileFactory;
use Domain\File\Models\File;
use Illuminate\Http\UploadedFile;
use Infrastructure\Repositories\Contracts\FileRepositoryContract;
use Shared\Exceptions\ServerErrorException;
use Tests\TestCase;

class YCloudS3ServiceTest extends TestCase
{
    /**
     * Сontains the service being tested
     *
     * @var YCloudS3Service
     */
    protected YCloudS3Service $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new YCloudS3Service(
            app(FileRepositoryContract::class),
        );
    }

    public function test_upload_success(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Data for testing
        $dto = $this->getFileDTO();
        $dto->user_id = $user->id;

        // Result from method of service
        $result = $this->service->upload($dto);

        // Assert that result instance of File Model
        $this->assertInstanceOf(File::class, $result);

        // Assert that File saved into yc s3
        $this->assertTrue($this->service->disk->exists($result->key));

        // Assert that database has File data
        $this->assertDatabaseHas(File::class, $dto->except('content')->toArray());
    }

    public function test_upload_unauth_and_user_id_is_empty(): void
    {
        // Data for testing
        $dto = $this->getFileDTO();

        // Expect server error exception
        $this->expectException(ServerErrorException::class);

        // Call method of service
        $this->service->upload($dto);

        // Assert that database missing File data
        $this->assertDatabaseMissing(File::class, $dto->except('content')->toArray());
    }

    protected function getFileDTO(): FileDTO
    {
        $factory = new FileFactory();

        $dto = FileDTO::from($factory->definition());

        $dto->content = UploadedFile::fake()->image('testing.jpg');

        return $dto;
    }
}
