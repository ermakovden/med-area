<?php

declare(strict_types=1);

namespace Tests\Unit\Application\S3\Commands;

use Application\S3\Commands\ForceDeleteFilesCommand;
use Domain\File\Models\File;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Jobs\File\DeleteFileJob;
use Shared\Enums\Storage as EnumsStorage;
use Tests\TestCase;

class ForceDeleteFilesCommandTest extends TestCase
{
    /**
     * Сontains the command being tested
     *
     * @var ForceDeleteFilesCommand
     */
    protected readonly ForceDeleteFilesCommand $command;

    protected readonly Filesystem $disk;

    protected string $subDays;

    public function setUp(): void
    {
        parent::setUp();

        $this->disk = Storage::disk(EnumsStorage::S3_TESTING);

        $this->command = new ForceDeleteFilesCommand($this->disk);

        $this->subDays = config('filesystems.environments.force_delete_sub_days');
    }

    public function test_handle_success(): void
    {
        // Init fake queue
        Queue::fake();

        // User for testing
        $user = $this->getUser();

        // Create testing data - File model
        $file = File::factory(state: [
            'deleted_at' => now()->subDays($this->subDays),
            'key' => $this->disk->putFile(
                'force-delete-command-test-handle-success',
                UploadedFile::fake()->image('force-delete-command-test-handle-success.jpg') // save file in s3 storage
            ),
        ])->for($user)->createOne();

        // Check assert that file model exists and soft deleted
        $this->assertSoftDeleted(File::class, ['id' => $file->id]);

        // Check assert that file exists in s3 storage
        $this->assertTrue($this->disk->exists($file->key));

        // Call method of command
        $this->command->handle();

        Queue::assertPushed(DeleteFileJob::class, function ($job) {
            $job->handle();
            return true;
        });

        // Check assert that file model force deleted
        $this->assertModelMissing($file);

        // Check assert that file was deleted from s3 storage
        $this->assertNotTrue($this->disk->exists($file->key));
    }

    public function test_handle_not_deleted_by_date(): void
    {
        // User for testing
        $user = $this->getUser();

        // Create testing data - File model
        $file = File::factory(state: [
            'deleted_at' => now()->subDays($this->subDays - 1),
            'key' => $this->disk->putFile(
                'force-delete-command-test-handle-success',
                UploadedFile::fake()->image('force-delete-command-test-handle-success.jpg') // save file in s3 storage
            ),
        ])->for($user)->createOne();

        // Check assert that file model exists and soft deleted
        $this->assertModelExists($file);
        $this->assertSoftDeleted(File::class, ['user_id' => $user->id]);

        // Check assert that file exists in s3 storage
        $this->assertTrue($this->disk->exists($file->key));

        // Call method of command
        $this->command->handle();

        // Check assert that file model exists and soft deleted
        $this->assertModelExists($file);
        $this->assertSoftDeleted(File::class, ['user_id' => $user->id]);

        // Check assert that file exists in s3 storage
        $this->assertTrue($this->disk->exists($file->key));
    }
}
