<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelRequest>
 */
class TravelRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destino' => $this->faker->city(),
            'data_ida' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'data_volta' => $this->faker->dateTimeBetween('+1 month', '+2 months')->format('Y-m-d'),
            'status' => 'solicitado',
            'updated_by' => null,
        ];
    }
}
