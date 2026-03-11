<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\File\Controllers;

use Application\S3\DTO\Filters\FilterFileDTO;
use Domain\File\Factories\FileFactory;
use Domain\File\Models\File as FileModel;
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

    public function test_index_success(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Data for testing
        $count = 3;
        FileModel::factory($count)->for($user)->create();

        // Filters for testing
        $filters = FilterFileDTO::from(['user_ids' => [$user->id]]);

        // Send API Request
        $response = $this->get(route('api.files.index', $filters->toArray()));

        // Check asserts that response success
        $response->assertOk();
        $response->assertJsonCount($count, 'data');
        $this->assertDatabaseHas(FileModel::class, ['user_id' => $user->id]);
    }

    public function test_index_validation_filter_user_ids(): void
    {
        // Auth user for testing
        $user = $this->authUser();
        $user2 = $this->getUser();

        // Filters for testing
        $filters = FilterFileDTO::from(['user_ids' => [$user->id, $user2->id]]);

        // Send API Request with filters and another user_id in url
        $response = $this->get(route('api.files.index', $filters->toArray()));

        // Check assert forbidden, 403 http code
        $response->assertUnprocessable();
        $response->assertInvalid(['user_ids.1']);
    }

    public function test_destroy_success_filter_by_user_ids(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Factory for testing
        $factory = new FileFactory();

        // Data for testing
        $files = [];
        $count = 3;
        for ($i = 0; $i < $count; $i++) {
            $files[] = $factory->for($user)->create([
                'key' => $this->disk->putFile(
                    'force-delete-command-test-handle-success',
                    UploadedFile::fake()->image('force-delete-command-test-handle-success.jpg'),
                ),
            ]);
        }

        // Filters for testing
        $filters = FilterFileDTO::from(['user_ids' => [$user->id]]);

        // Send API Request
        $response = $this->delete(route('api.files.destroy'), $filters->toArray());

        // Check asserts that files deleted with status code 204
        $response->assertNoContent();
        foreach ($files as $file) {
            $this->assertSoftDeleted($file);
        }
    }

    public function test_destroy_success_filter_by_ids(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Factory for testing
        $factory = new FileFactory();

        // Filters for testing
        $filters = FilterFileDTO::from(['ids' => []]);

        // Data for testing
        $files = [];
        $count = 3;
        for ($i = 0; $i < $count; $i++) {
            $file = $factory->for($user)->create([
                'key' => $this->disk->putFile(
                    'force-delete-command-test-handle-success',
                    UploadedFile::fake()->image('force-delete-command-test-handle-success.jpg'),
                ),
            ]);

            $filters->ids[] = $file->id;
            $files[] = $file;
        }

        // Send API Request
        $response = $this->delete(route('api.files.destroy'), $filters->toArray());

        // Check asserts that files deleted with status code 204
        $response->assertNoContent();
        foreach ($files as $file) {
            $this->assertSoftDeleted($file);
        }
    }

    public function test_destroy_validation_filter_user_ids(): void
    {
        // Auth user for testing
        $user = $this->authUser();
        $user2 = $this->getUser();

        // Filters for testing
        $filters = FilterFileDTO::from(['user_ids' => [$user->id, $user2->id]]);

        // Send API Request with bad filters
        $response = $this->delete(route('api.files.destroy', $filters->toArray()));

        // Check asserts user_ids filter errors
        $response->assertUnprocessable();
        $response->assertInvalid(['user_ids.1']);
    }
}
