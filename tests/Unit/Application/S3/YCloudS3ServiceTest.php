<?php

declare(strict_types=1);

namespace Tests\Unit\Application\S3;

use Application\S3\DTO\FileDTO;
use Application\S3\DTO\Filters\FilterFileDTO;
use Application\S3\Services\YCloudS3Service;
use Domain\File\Factories\FileFactory;
use Domain\File\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Repositories\Contracts\FileRepositoryContract;
use Shared\Enums\Storage as EnumsStorage;
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
            Storage::disk(EnumsStorage::S3_TESTING),
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

    public function test_get_files_success(): void
    {
        // Filters for testing
        $filters = FilterFileDTO::from([]);

        // Result from method of service
        $result = $this->service->getFiles($filters);

        // Check asserts
        $this->assertInstanceOf(File::class, $result->first());
    }

    public function test_get_files_success_use_filter_by_user_ids(): void
    {
        // User for testing
        $user = $this->getUser();

        // Create Files for User for testing filter
        $count = 3;
        File::factory()->for($user)->createMany($count);

        // Filters for testing
        $filters = FilterFileDTO::from([
            'user_ids' => [$user->id],
        ]);

        // Result from method of service
        $result = $this->service->getFiles($filters);

        // Check asserts
        $this->assertInstanceOf(File::class, $result->first());
        $this->assertCount($count, $result);
    }

    public function test_get_files_success_use_filter_by_size(): void
    {
        // User for testing
        $user = $this->getUser();

        // Create Files with size for testing filter
        $count = 3;
        File::factory(state: ['size' => 100])->for($user)->createMany($count);

        // Filters for testing
        $filters = FilterFileDTO::from([
            'min_size' => 99,
            'max_size' => 101,
        ]);

        // Result from method of service
        $result = $this->service->getFiles($filters);

        // Check asserts
        $this->assertInstanceOf(File::class, $result->first());
        $this->assertCount($count, $result);


        // Create Files with size for testing filter
        $count = 3;
        File::factory(state: ['size' => 100])->for($user)->createMany($count);

        // Filters for testing
        $filters = FilterFileDTO::from(['min_size' => 101]);

        // Result from method of service
        $result = $this->service->getFiles($filters);

        // Check asserts that not found
        $this->assertCount(0, $result);


        // Filters for testing
        $filters = FilterFileDTO::from(['max_size' => 0]);

        // Result from method of service
        $result = $this->service->getFiles($filters);

        // Check asserts that not found
        $this->assertCount(0, $result);
    }

    protected function getFileDTO(): FileDTO
    {
        $factory = new FileFactory();

        $dto = FileDTO::from($factory->definition());

        $dto->content = UploadedFile::fake()->image('testing.jpg');

        return $dto;
    }
}
