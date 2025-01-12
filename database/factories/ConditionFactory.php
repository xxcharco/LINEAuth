<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Condition>
 */
class ConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recorded_date' => $this->faker->date(),
            'is_high' => $this->faker->boolean,
            'condition' => $this->faker->randomElement(['良い', 'やや良い', 'やや悪い', '悪い']),
        ];
    }
}
