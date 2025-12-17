<?php

declare(strict_types=1);

namespace Domain\File\Factories;

use Domain\File\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\File\Models\File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->uuid(),
            'storage' => 's3-testing',
            'endpoint' => config('filesystems.disks.s3-testing.endpoint'),
            'bucket' => config('filesystems.disks.s3-testing.bucket'),
            'key' => microtime() . random_int(1, 99999),
            'size' => fake()->randomDigit(),
        ];
    }
}
