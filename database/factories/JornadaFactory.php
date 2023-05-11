<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JornadaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombreJornada' => $this->faker->randomElement(['MAÑANA', 'TARDE', 'NOCHE']),
            'descripcion'   => $this->faker->text(),
            'horaInicial'   => $this->faker->randomElement(['12:12:12', '01:01:01']),
            'horaFinal'     => $this->faker->randomElement(['07:12:12', '06:01:01']),
            'numeroHoras'   => $this->faker->randomElement([12, 7]),
        ];
    }
}
