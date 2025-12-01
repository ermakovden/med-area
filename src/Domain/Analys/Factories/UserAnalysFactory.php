<?php

declare(strict_types=1);

namespace Domain\Analys\Factories;

use Domain\Analys\Enums\Analys;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Analys\Models\UserAnalys>
 */
class UserAnalysFactory extends Factory
{
    protected $model = UserAnalys::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $analys = Analys::from(random_int(1, count(Analys::cases())));

        return [
            'user_id' => fake()->uuid(),
            'analys_id' => $analys->value,
            'analys_name' => $analys->name,
            'data' => fake()->randomDigit(),
        ];
    }
}
