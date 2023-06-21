<?php

namespace Database\Factories;

use App\Models\EstadoGrupo;
use App\Models\NivelFormacion;
use App\Models\Programa;
use App\Models\TipoFormacion;
use App\Models\TipoGrupo;
use App\Models\TipoOferta;
use Illuminate\Database\Eloquent\Factories\Factory;

class GrupoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $tipoGrupo       = TipoGrupo::all()->random();
        $programa        = Programa::all()->random();
        $nivelFormacion  = NivelFormacion::all()->random();
        $tipoFormacion   = TipoFormacion::all()->random();
        $estado          = EstadoGrupo::all()->random();
        $tipoOferta      = TipoOferta::all()->random();

        return [

            'nombre'            => $this->faker->unique()->randomFloat(0, 0, 1000000),
            'fechaInicialGrupo' => $this->faker->randomElement(['2018/12/12', '20/12/12']),
            'fechaFinalGrupo'   => $this->faker->randomElement(['2023/12/10', '2030/12/10']),
            'observacion'       => strtoupper($this->faker->text()),
            'idTipoGrupo'       => $tipoGrupo -> id,
            'idPrograma'        => $programa -> id,
            'idNivel'           => $nivelFormacion -> id,
            'idTipoFormacion'   => $tipoFormacion -> id,
            'idEstado'          => $estado -> id,
            'idTipoOferta'      => $tipoOferta -> id,

        ];
    }
}
