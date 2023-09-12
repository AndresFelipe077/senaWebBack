<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\EstadoPrograma;
use App\Models\Status;
use App\Models\TipoProgramas;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $area           = Area::all()->random();
        $estadoPrograma = EstadoPrograma::all()->random();
        $tipoPrograma   = TipoProgramas::all()->random();
        $estado         = Status::all()->random();

        return [
            'nombrePrograma'      => $this->faker->randomElement(['PROGRAMA U 1', 'PROGRAMA 2']),
            'codigoPrograma'      => $this->faker->randomElement(['jqjwhwd8912', 'ifm23f8']),
            'descripcionPrograma' => $this->faker->randomElement(['DESCRIPCION PROGRAMA 1', 'DESCRIPCION PROGRAMA 2']),
            'totalHoras'          => $this->faker->randomElement([5, 10]),
            'etapaLectiva'        => $this->faker->randomElement([15, 10]),
            'etapaProductiva'     => $this->faker->randomElement([5, 10]),
            'creditosLectiva'     => $this->faker->randomElement([5, 2]),
            'creditosProductiva'  => $this->faker->randomElement([15, 5]),
            'version'             => $this->faker->randomElement(['1', '2']),

            'idTipoPrograma'      => $tipoPrograma->id,
            'idEstado'            => $estadoPrograma->id,
            'idArea'              => $area->id
        ];
    }
}
