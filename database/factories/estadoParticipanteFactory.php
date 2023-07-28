<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class estadoParticipanteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $estadoParticipante = ['ACTIVO', 'PENDIENTE', 'TRASLADO','CANCELADO','RETIRO VOLUNTARIO','DESERTADO'];
        shuffle($estadoParticipante); // Reorganizar aleatoriamente la lista de niveles
        $estadoParticipanteAleatorio = array_pop($estadoParticipante); // Seleccionar el último elemento de la lista reorganizada

        return [
            'detalleEstado' => $estadoParticipanteAleatorio,
        ];
    }
}
