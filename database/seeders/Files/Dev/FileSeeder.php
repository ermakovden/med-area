<?php

declare(strict_types=1);

namespace Database\Seeders\Files\Dev;

use Domain\File\Models\File;
use Domain\User\Models\User;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        File::factory()
            ->for(User::factory()->createOne())
            ->createMany(10);
    }
}
