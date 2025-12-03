<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Analysis\AnalysisEnumSeeder;
use Database\Seeders\Analysis\Dev\UserAnalysSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AnalysisEnumSeeder::class);

        if (! app()->isProduction()) {
            $this->call(UserAnalysSeeder::class);
        }
    }
}
