<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\File\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Shared\Enums\Storage as EnumsStorage;
use Tests\TestCase;
use Illuminate\Http\Testing\File;

class FileControllerTest extends TestCase
{
    protected Filesystem $disk;

    /** @var array<File> */
    protected array $files;

    public function setUp(): void
    {
        parent::setUp();

        $this->disk = Storage::disk(EnumsStorage::S3_TESTING->value);

        $this->files = [
            UploadedFile::fake()->create('test1.jpg', 1024),
            UploadedFile::fake()->create('test2.pdf', 1024),
        ];
    }

    public function test_upload_success(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Send API Request
        $response = $this->post(route('api.files.upload'), [
            'user_id' => $user->id,
            'files' => $this->files,
        ]);

        // Check asserts
        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'path',
                ],
            ],
        ]);
    }

    public function test_upload_validation_big_size(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Send API Request
        $response = $this->post(route('api.files.upload'), [
            'user_id' => $user->id,
            'files' => [
                UploadedFile::fake()->create('bigimage.jpg', 15001), // Limit: 15000 / 150001 kb
            ],
        ]);

        // Check asserts
        $response->assertUnprocessable();
        $response->assertInvalid(['files.0']);
    }

    public function test_upload_unauth(): void
    {
        // User for testing
        $user = $this->getUser();

        // Send API Request
        $response = $this->post(route('api.files.upload'), [
            'user_id' => $user->id,
            'files' => $this->files,
        ]);

        // Check assert that unauthorized
        $response->assertUnauthorized();
    }

    public function test_upload_unreal_user_id(): void
    {
        // Auth user for testing
        $this->authUser();

        // User with another user_id for testing
        $user2 = $this->getUser();

        // Send API Request
        $response = $this->post(route('api.files.upload'), [
            'user_id' => $user2->id, // another user_id
            'files' => $this->files,
        ]);

        // Check asserts
        $response->assertUnprocessable();
        $response->assertInvalid(['user_id']);
        $response->assertJsonValidationErrors([
            'user_id' => ['The user id must match the authenticated user ID.'],
        ]);
    }

    public function test_upload_validation_empty_values(): void
    {
        // Auth user for testing
        $this->authUser();

        // Send API Request
        $response = $this->post(route('api.files.upload'), [
            'user_id' => '', // empty user_id
            'files' => [], // empty files
        ]);

        // Check asserts that 422 and files is empty
        $response->assertUnprocessable();
        $response->assertInvalid(['user_id', 'files']);
    }
}
