<?php

declare(strict_types=1);

namespace Database\Seeders\Analysis;

use Domain\Analys\Enums\Analys as AnalysEnum;
use Domain\Analys\Models\Analys;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnalysisEnumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (AnalysEnum::cases() as $analys) {
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
