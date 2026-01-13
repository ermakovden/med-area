<?php

declare(strict_types=1);

namespace Domain\AI\Recognise\Factories;

use Domain\AI\Recognise\Enums\RecogniseStatus;
use Domain\AI\Recognise\Models\RecogniseRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\AI\Recognise\Models\RecogniseRequest>
 */
class RecogniseRequestFactory extends Factory
{
    protected $model = RecogniseRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->uuid(),
            'file_id' => fake()->uuid(),
            'operation_id' => fake()->uuid(),
            'response' => [fake()->word() => fake()->word],
            'status' => fake()->randomElement(RecogniseStatus::cases()),
        ];
    }
}
