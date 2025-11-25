<?php

declare(strict_types=1);

namespace Database\Seeders\Analysis\Dev;

use Domain\Analysis\Models\UserAnalys;
use Domain\User\Models\User;
use Illuminate\Database\Seeder;

class UserAnalysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < random_int(5, 10); $i++) {
            UserAnalys::factory(random_int(50, 100))
                ->for(User::factory())
                ->createMany();
        }
    }
}
