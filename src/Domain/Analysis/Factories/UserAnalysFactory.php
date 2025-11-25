<?php

declare(strict_types=1);

namespace Domain\Analysis\Factories;

use Domain\Analysis\Enums\Analysis;
use Domain\Analysis\Models\UserAnalys;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Analysis\Models\UserAnalys>
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
        $analys = Analysis::from(random_int(1, count(Analysis::cases()) - 1));

        return [
            'user_id' => fake()->uuid(),
            'analys_id' => $analys->value,
            'analys_name' => $analys->name,
            'data' => fake()->randomDigit(),
        ];
    }
}
