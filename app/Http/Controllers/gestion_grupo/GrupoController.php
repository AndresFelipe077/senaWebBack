<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\AsignacionJornadaGrupo;
use App\Models\AsignacionParticipante;
use App\Models\Grupo;
use App\Models\HorarioInfraestructuraGrupo;
use ArrayObject;
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
            'idPrograma' => $data['idPrograma'],
            'idNivel' => $data['idNivel'],
            'idTipoFormacion' => $data['idTipoFormacion'],
            'idEstado' => $data['idEstado'],
            'idTipoOferta' => $data['idTipoOferta'],
        ]);
        $grupo->save();

        $jornadas = $data['jornadas'];

        foreach ($jornadas as $grupoJItem) {
            $this->guardarGruposJorna($grupoJItem, $grupo->id);
        }

        $infraestructuras = $data['infraestructuras'];

        foreach ($infraestructuras as $infraItem) {
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
            'idTipoOferta' => $data['idTipoOferta']
        ]);

        $grupos_jornada = $data['jornadas'];
        if ($grupos_jornada) {
            //obtiene los id de los grupoJornada que aun estan presentes en el array
            $gruposJornadaIds = array_column($grupos_jornada, 'id');

            //Elimina aquellos registros de la tabla culla id no este en el array de arriba
            /*AsignacionJornadaGrupo::whereNotNull('id')
                ->whereNotIn('id', $gruposJornadaIds)
                ->where('idGrupo', $id)
                ->delete();*/

            foreach ($grupos_jornada as $grupoJItem) {
                $this->actualizarGruposJorna($grupoJItem, $grupo->id);
            }
        } else {
            AsignacionJornadaGrupo::where('idGrupo', $id)->delete();
        }

        $infraestructuras = $data['infraestructuras'];
        if ($infraestructuras) {
            foreach ($infraestructuras as $horarioInfraItem) {
                $this->actualizarHorarioInfra($horarioInfraItem, $grupo->id);
            }
        } else {
            HorarioInfraestructuraGrupo::where('idGrupo', $id)->delete();
        }

        if (isset($request->participantes)) {
            AsignacionParticipante::where('idGrupo', $id)->delete();
            foreach ($request->participantes as $val) {
                foreach ($val as $val2) {
                    $info = ['idGrupo' => $grupo->id, 'idParticipante' => $val2];
                    $participante = new AsignacionParticipante($info);
                    $participante->save();
                }
            }
        }
        //print_r('Jornadas:' . count($grupo['jornadas']) . '- Infraestructuras:' . count($grupo['infraestructuras']));
        //return response()->json($grupo);
    }

    private function actualizarGruposJorna(array $data, int $idGrupo)
    {
        $idJornada = $data['jornada_grupo']['idJornada'];

        //revisa si ya existe  un registro igual
        $repeated = AsignacionJornadaGrupo::where('idJornada', $idJornada)
            ->where('idGrupo', $idGrupo)->exists();
        //en caso de existir, no actualiza o guarda el registro
        if ($repeated) {
            return;
        }
        /**verifica si el objeto tiene una id ,
         * en caso de no tenerla, crea un nuevo registro*/ 
        $hasId = boolval(isset($data['jornada_grupo']['id']));
        if ($hasId) {
            $idGJornada = $data['jornada_grupo']['id'];
            var_dump($idGJornada);
            $grupo_jornada = AsignacionJornadaGrupo::where('id',$idGJornada) -> first();
            var_dump($grupo_jornada);
            if($grupo_jornada){
                $grupo_jornada->idJornada = $idJornada;
                $grupo_jornada->idGrupo = $idGrupo;

                $grupo_jornada->save();
            }else{
                unset($data['id']);
                $this->guardarGruposJorna($data, $idGrupo);
            }
        } else {
            $this->guardarGruposJorna($data, $idGrupo);
        }
    }
    private function actualizarHorarioInfra(array $data, int $idGrupo)
    {
        $repeated = HorarioInfraestructuraGrupo::where('idInfraestructura', $data['horario_infraestructura']['idInfraestructura'])
            ->where('idGrupo', $idGrupo)
            ->where('fechaInicial', $data['horario_infraestructura']['fechaInicial'])
            ->where('fechaFinal', $data['horario_infraestructura']['fechaFinal']);
        if ($repeated) {
            return;
        }
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
        $grupo = Grupo::findOrFail($id);
        $grupo->delete();
        return response()->json([
            'eliminada'
        ]);
    }
    public function eliminarGruposJorna($grupos_jornada, int $idGrupo)
    {
        //trae los grupos jornada actualmente asignados
        $gruposJornada = AsignacionJornadaGrupo::where('idGrupo', $idGrupo)->get();
        //revisa que jornadas ya no estan asignadas y las elimina
        $grupos_jornada = $gruposJornada->map(function ($grupoJornada) use ($grupos_jornada) {
            if (!in_array($grupoJornada, $grupos_jornada)) {
                $grupoJornada->delete();
                return;
            }
            print_r('eliminado');
            return $grupoJornada;
        });
        return $grupos_jornada;
    }
}
