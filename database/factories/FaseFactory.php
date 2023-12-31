<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombreFase'    => $this->faker->text(),
            'codigoFase'    => $this->faker->text(),
        ];
    }
}
