<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class tipoParticipacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tipoParticipacion = ['VOCERO', 'SUPLENTE', 'LIDER'];
        shuffle($tipoParticipacion); // Reorganizar aleatoriamente la lista de niveles
        $tipoParticipacionAleatorio = array_pop($tipoParticipacion); // Seleccionar el último elemento de la lista reorganizada

        return [
            'detalleParticipacion' => $tipoParticipacionAleatorio,
        ];
    }
}
