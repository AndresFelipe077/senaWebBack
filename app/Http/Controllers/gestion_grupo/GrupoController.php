<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\AsignacionJornadaGrupo;
use App\Models\AsignacionParticipante;
use App\Models\Grupo;
use App\Models\HorarioInfraestructuraGrupo;
use App\Models\Infraestructura;
use ArrayObject;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $grupos = Grupo::with([
            'tipoGrupo',
            'programa',
            // 'instructor.persona',
            'nivelFormacion',
            'tipoFormacion',
            'estadoGrupo',
            'tipoOferta',
            'jornadas',
            'participantes',
            'infraestructuras'
        ])->get();

        //quitar pivots
        $newGrupos = $grupos->map(function ($grupo) {
            $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
                $pivot = $infr['pivot'];
                unset($infr['pivot']);
                $infr['horario_infraestructura'] = $pivot;
                return $infr;
            });

            $grupo['jornadas'] = $grupo['jornadas']->map(function ($jornada) {
                $pivot = $jornada['pivot'];
                unset($jornada['pivot']);
                $jornada['jornada_grupo'] = $pivot;
                return $jornada;
            });
            return $grupo;
        });
        return response()->json($newGrupos);
        //return response()->json($grupos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $data = $request->all();
        $grupo = new Grupo([
            'nombre' => $data['nombre'],
            'fechaInicialGrupo' => $data['fechaInicialGrupo'],
            'fechaFinalGrupo' => $data['fechaFinalGrupo'],
            'observacion' => $data['observacion'],
            'idTipoGrupo' => $data['idTipoGrupo'],
            //   'idLider' => $data['idLider'],
            'idPrograma' => $data['idPrograma'],
            'idNivel' => $data['idNivel'],
            'idTipoFormacion' => $data['idTipoFormacion'],
            'idEstado' => $data['idEstado'],
            'idTipoOferta' => $data['idTipoOferta'],
        ]);

        $grupo->save();

        foreach ($request->jornadas as $jornadaItem) {
            foreach ($jornadaItem as $jItem) {
                $info = ['idGrupo' => $grupo->id, 'idJornada' => $jItem];
                $asignacionJornadaGrupo = new AsignacionJornadaGrupo($info);
                $asignacionJornadaGrupo->save();
            }
        }

        // $infraestructuras = $data['infraestructuras'];

        // foreach ($infraestructuras as $infraItem) {
        //   $this->guardarHorarioInfra($infraItem, $grupo->id);
        // }


        $infraestructuras = $data['infraestructuras'];

        // foreach ($infraestructuras as $infraItem) {
        //     try {
        //         $infraestructura = Infraestructura::findOrFail($infraItem['id']);
        //     } catch (ModelNotFoundException $e) {
        //         return response()->json(['error' => 'La infraestructura no existe.'], 404);
        //     }

        //     // Verificar si la infraestructura ya está asignada a otro grupo en la misma fecha
        //     $existeAsignacion = HorarioInfraestructuraGrupo::where('idInfraestructura', $infraestructura->id)
        //         ->where(function ($query) use ($grupo) {
        //             $query->where('idGrupo', '<>', $grupo->id)
        //                 ->where('fechaInicial', '<=', $grupo->fechaFinalGrupo)
        //                 ->where('fechaFinal', '>=', $grupo->fechaInicialGrupo);
        //         })
        //         ->exists();

        //     if ($existeAsignacion) {
        //         return response()->json(['error' => 'La infraestructura seleccionada ya está asignada a otro grupo en la misma fecha.'], 422);
        //     } else {
        //         $this->guardarHorarioInfra($infraItem, $grupo->id);
        //     }
        // }

        foreach ($infraestructuras as $infraItem) {
            try {
                $infraestructura = Infraestructura::findOrFail($infraItem['id']);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'La infraestructura no existe.'], 404);
            }

            $existeAsignacion = HorarioInfraestructuraGrupo::where('idInfraestructura', $infraestructura->id)
                ->where(function ($query) use ($grupo) {
                    $query->where('idGrupo', '<>', $grupo->id)
                        ->where(function ($query) use ($grupo) {
                            $query->where('fechaInicial', '<=', $grupo->fechaFinalGrupo)
                                ->where('fechaFinal', '>=', $grupo->fechaInicialGrupo);
                        })
                        ->whereHas('grupo.jornadas', function ($query) use ($grupo) {
                            $query->whereIn('jornada.id', $grupo->jornadas->pluck('id'));
                        });
                })
                ->exists();

            if ($existeAsignacion) {
                return response()->json(['error' => 'La infraestructura seleccionada ya está asignada a otro grupo en la misma fecha o jornada.'], 422);
            }

            $this->guardarHorarioInfra($infraItem, $grupo->id);
        }




        return response()->json($grupo, 201);
    }

    private function guardarGruposJorna(array $data, int $idGrupo)
    {
        $jornada = new AsignacionJornadaGrupo([
            'idJornada' => $data['jornada_grupo']['idJornada'],
            'idGrupo' => $idGrupo
        ]);
        $jornada->save();
    }

    private function guardarHorarioInfra(array $data, int $idGrupo)
    {
        $horarioInfraestructura = new HorarioInfraestructuraGrupo([
            'idGrupo' => $idGrupo,
            'idInfraestructura' => $data['horario_infraestructura']['idInfraestructura'],
            'fechaInicial'      => $data['horario_infraestructura']['fechaInicial'],
            'fechaFinal'        => $data['horario_infraestructura']['fechaFinal']
        ]);
        $horarioInfraestructura->save();
    }

    /**
     * search a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function buscarGrupos(Request $request)
    {
        $grupo = $request->get('grupo');

        $querys = Grupo::with('tipogrupo')->where('nombre', 'LIKE', '%' . $grupo . '%')->get();

        return response()->json($querys);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models$grupo  $grupo
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $dato = Grupo::with([
            'tipoGrupo',
            'programa',
            'instructor.persona',
            'nivelFormacion',
            'tipoFormacion',
            'estadoGrupo',
            'tipoOferta',
            'jornadas',
            'participantes',
            'infraestructuras'
        ])->find($id);

        if (!$dato) {
            return response()->json(['error' => 'El dato no fue encontrado'], 404);
        }

        $dato['infraestructuras'] = $dato['infraestructuras']->map(function ($infr) {
            $pivot = $infr['pivot'];
            unset($infr['pivot']);
            $infr['horario_infraestructura'] = $pivot;
            return $infr;
        });

        $dato['jornadas'] = $dato['jornadas']->map(function ($jornada) {
            $pivot = $jornada['pivot'];
            unset($jornada['pivot']);
            $jornada['jornada_grupo'] = $pivot;
            return $jornada;
        });

        return response()->json($dato);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models$grupo  $grupo
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $grupo = Grupo::findOrFail($id);

        $grupo->update([
            'nombre' => $data['nombre'],
            'fechaInicialGrupo' => $data['fechaInicialGrupo'],
            'fechaFinalGrupo' => $data['fechaFinalGrupo'],
            'observacion' => $data['observacion'],
            'idTipoGrupo' => $data['idTipoGrupo'],
            'idPrograma' => $data['idPrograma'],
            'idNivel' => $data['idNivel'],
            'idTipoFormacion' => $data['idTipoFormacion'],
            'idEstado' => $data['idEstado'],
            'idTipoOferta' => $data['idTipoOferta'],
        ]);

        // Eliminar todas las relaciones existentes en la tabla central
        // $grupo->jornadas()->detach();

        // $grupos_jornada = $data['jornadas'];

        // if ($grupos_jornada) {
        //   foreach ($grupos_jornada as $grupoJItem) {
        //     $this->actualizarGruposJorna($grupoJItem, $grupo->id);
        //   }
        // }


        $grupo->infraestructuras()->detach();

        $infraestructura = $data['infraestructuras'];

        foreach ($infraestructura as $horarioInfraItem) {
            $this->actualizarHorarioInfra($horarioInfraItem, $grupo->id);
        }

        AsignacionJornadaGrupo::where('idGrupo', $grupo->id)->delete();

        foreach ($request->jornadas as $jornaItem) {
            foreach ($jornaItem as $jItem) {
                $info = ['idGrupo' => $grupo->id, 'idJornada' => $jItem];
                $asignacionJornadaGrupo = new AsignacionJornadaGrupo($info);
                $asignacionJornadaGrupo->save();
            }
        }

        return response()->json($grupo, 200);
    }


    private function actualizarGruposJorna(array $data, int $idGrupo)
    {

        if ($data === null) {
            // Realizar acciones cuando $data es null
            $this->guardarGruposJorna([], $idGrupo); // Llamar a la función guardarGruposJorna con un arreglo vacío
            return;
        }

        $dataId = isset($data['id']) ? $data['id'] : 0;
        $grupo_jornada = AsignacionJornadaGrupo::find($dataId);
        if ($grupo_jornada) {
            $grupo_jornada->idJornada = $data['jornada_grupo']['idJornada'];
            $grupo_jornada->idGrupo = $idGrupo;

            $grupo_jornada->save();
        } else {
            unset($data['id']);
            $this->guardarGruposJorna($data, $idGrupo);
        }
    }

    private function actualizarHorarioInfra(array $data, int $idGrupo)
    {
        $dataId = isset($data['id']) ? $data['id'] : 0;
        $horario_infra = HorarioInfraestructuraGrupo::find($dataId);

        if ($horario_infra) {
            $horario_infra->idInfraestructura = $data['horario_infraestructura']['idInfraestructura'];
            $horario_infra->idGrupo = $idGrupo;
            $horario_infra->fechaInicial = $data['horario_infraestructura']['fechaInicial'];
            $horario_infra->fechaFinal = $data['horario_infraestructura']['fechaFinal'];

            $horario_infra->save();
        } else {
            unset($data['id']);
            $this->guardarHorarioInfra($data, $idGrupo);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models$grupo  $grupo
     * @return \Illuminate\Http\Response
     */

    public function destroy(int $id)
    {
        $newjornada = Grupo::findOrFail($id);
        $newjornada->delete();
        return response()->json([
            'eliminada'
        ]);
    }
}
