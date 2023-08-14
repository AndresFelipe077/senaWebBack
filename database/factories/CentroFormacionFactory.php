<?php

namespace Database\Factories;

use App\Models\regional;
use Illuminate\Database\Eloquent\Factories\Factory;

class CentroFormacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $regional  = regional::all()->random();

        return [
            'nombreCentro'  => $this->faker->randomElement(['PROGRAMA 1', 'PROGRAMA 2']),
            'idRegional'    => $regional->id,
        ];
    }
}
