<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Analysis\AnalysisEnumSeeder;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AnalysisEnumSeeder::class);
    }
}
