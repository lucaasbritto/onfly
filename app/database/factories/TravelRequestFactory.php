<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelRequest>
 */
class TravelRequestFactory extends Factory
{
    protected array $cidades = [
        'São Paulo', 'Rio de Janeiro', 'Salvador', 'Brasília', 'Belo Horizonte',
        'Curitiba', 'Fortaleza', 'Manaus', 'Recife', 'Porto Alegre', 'São Luis' , 'Aracaju', 'Florianópolis'
    ];

    public function definition(): array
    {
        $dataIda = $this->faker->dateTimeBetween('+1 days', '+1 month');
        $dataVolta = (clone $dataIda)->modify('+'.rand(2, 10).' days');

        return [
            'user_id' => User::factory(),
            'destino'     => $this->faker->randomElement($this->cidades),
            'data_ida'    => $dataIda->format('Y-m-d'),
            'data_volta'  => $dataVolta->format('Y-m-d'),
            'status' => 'solicitado',
            'updated_by' => null,
        ];
    }
}
