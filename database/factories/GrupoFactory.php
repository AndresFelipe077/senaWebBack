<?php

namespace Database\Factories;

use App\Models\EstadoGrupo;
use App\Models\Infraestructura;
use App\Models\NivelFormacion;
use App\Models\Programa;
use App\Models\Status;
use App\Models\TipoFormacion;
use App\Models\TipoGrupo;
use App\Models\TipoOferta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

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
        $lider           = User::all()->random();
        $programa        = Programa::all()->random();
        $infraestructura = Infraestructura::all()->random();
        $nivelFormacion  = NivelFormacion::all()->random();
        $tipoFormacion   = TipoFormacion::all()->random();
        $estado          = EstadoGrupo::all()->random();
        $tipoOferta      = TipoOferta::all()->random();

        return [
            'nombre'            => $this->faker->randomElement(['GRUPO 1', 'GRUPO 2']),
            'fechaInicialGrupo'      => $this->faker->randomElement(['2012/12/12', '2018/12/12']),
            'fechaFinalGrupo'        => $this->faker->randomElement(['2020/12/10', '2021/12/10']),
            'observacion'       => strtoupper($this->faker->text()),
            'idTipoGrupo'       => $tipoGrupo -> id,
            'idLider'           => $lider -> id,
            'idPrograma'        => $programa -> id,
            // 'idInfraestructura' => $infraestructura -> id,
            'idNivel'           => $nivelFormacion -> id,
            'idTipoFormacion'   => $tipoFormacion -> id,
            'idEstado'          => $estado -> id,
            'idTipoOferta'      => $tipoOferta -> id,

        ];
    }
}
