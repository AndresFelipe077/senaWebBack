<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Competencias;

class CompetenciasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */


    //  para poder hacer datos de prueba ...............
    public function definition()
    {
        return [
            'nombreCompetencia' => $this->faker->text(),
            'codigoCompetencia' => $this->faker->text(),
        ];
    }
}
