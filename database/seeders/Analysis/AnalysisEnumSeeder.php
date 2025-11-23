<?php

declare(strict_types=1);

namespace Database\Seeders\Analysis;

use Domain\Analysis\Enums\Analysis;
use Domain\Analysis\Models\Analys;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnalysisEnumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Analysis::cases() as $analys) {
            Analys::updateOrCreate(
                [
                    'id' => $analys->value,
                ],
                [
                    'id' => $analys->value,
                    'name' => strtolower($analys->name),
                ]
            );
        }
    }
}
