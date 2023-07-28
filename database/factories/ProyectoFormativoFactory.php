<?php

namespace Database\Factories;

use App\Models\CentroFormacion;
use App\Models\Fase;
use App\Models\Programa;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProyectoFormativoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $programa         = Programa::all()->random();
        $centroFormacion  = CentroFormacion::all()->random();

        return [
            'nombre'            => $this->faker->randomElement(['PROYECTO FORMATIVO 1', 'PROYECTO FORMATIVO 2']),
            'codigo'            => $this->faker->randomElement([2502, 2342]),
            'idPrograma'        => $programa->id,
            'tiempoEstimado'    => $this->faker->randomElement([12, 40]),
            'numeroTotalRaps'   => $this->faker->randomElement([2, 3]),
            'idCentroFormacion' => $centroFormacion->id,
        ];
    }
}
