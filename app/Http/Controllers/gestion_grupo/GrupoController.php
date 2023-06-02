<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\AsignacionJornadaGrupo;
use App\Models\AsignacionParticipante;
use App\Models\Grupo;
use App\Models\HorarioInfraestructuraGrupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $tipoGrupo       = $request->input('tipogrupo');
        $instructor      = $request->input('idpersona');
        $programa        = $request->input('nombrePrograma');
        $infraestructura = $request->input('horarioInfraestructuraGrupo');
        $nivelFormacion  = $request->input('nivel');
        $tipoFormacion   = $request->input('nombreTipoFormacion');
        $estadoGrupo     = $request->input('nombreEstado');
        $tipoOferta      = $request->input('nombreOferta');
        $gruposJornada   = $request->input('grupos_jornada');

        $grupos = Grupo::with([
            'tipoGrupo',
            'programa',
            'instructor.persona',
            'infraestructura',                                                // y ademas los campos restantes de la tabla intermedia
            'nivelFormacion',
            'tipoFormacion',
            'estadoGrupo',
            'tipoOferta',
            'gruposJornada',
            'participantes' => function ($query) {
                $query->withPivot('fechaInicial', 'fechaFinal', 'descripcion');
            }
        ]);

        if ($tipoGrupo) {
            $grupos->whereHas('grupos', function ($q) use ($tipoGrupo) {
                return $q->select('id')->where('id', $tipoGrupo)->orWhere('nombreTipoGrupo', $tipoGrupo);
            });
        }

        if ($instructor) {
            $grupos->whereHas('idpersona', function ($q) use ($instructor) {
                return $q->select('id')->where('id', $instructor)->orWhere('contrasena', $instructor);
            });
        }

        if ($programa) {
            $grupos->whereHas('nombrePrograma', function ($q) use ($programa) {
                return $q->select('id')->where('id', $programa)->orWhere('nombrePrograma', $programa);
            });
        }

        if ($nivelFormacion) {
            $grupos->whereHas('nivelFormacion', function ($q) use ($nivelFormacion) {
                return $q->select('id')->where('id', $nivelFormacion)->orWhere('nivelFormacion', $nivelFormacion);
            });
        }

        if ($tipoFormacion) {
            $grupos->whereHas('nombreTipoFormacion', function ($q) use ($tipoFormacion) {
                return $q->select('id')->where('id', $tipoFormacion)->orWhere('nombreTipoFormacion', $tipoFormacion);
            });
        }

        if ($estadoGrupo) {
            $grupos->whereHas('estadoGrupo', function ($q) use ($estadoGrupo) {
                return $q->select('id')->where('id', $estadoGrupo)->orWhere('nombreEstado', $estadoGrupo);
            });
        }

        if ($tipoOferta) {
            $grupos->whereHas('nombreOferta', function ($q) use ($tipoOferta) {
                return $q->select('id')->where('id', $tipoOferta)->orWhere('nombreOferta', $tipoOferta);
            });
        }

        return response()->json($grupos->get());
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
            'idLider' => $data['idLider'],
            'idPrograma' => $data['idPrograma'],
            'idNivel' => $data['idNivel'],
            'idTipoFormacion' => $data['idTipoFormacion'],
            'idEstado' => $data['idEstado'],
            'idTipoOferta' => $data['idTipoOferta'],
        ]);
        $grupo->save();

        $grupos_jornada = $data['grupos_jornada'];

        foreach ($grupos_jornada as $grupoJItem) {
            $this -> guardarGruposJorna($grupoJItem,$grupo->id);
        }

        $infraestructura = $data['infraestructura'];

       foreach ($infraestructura as $infraItem) {
            $this->guardarHorarioInfra($infraItem, $grupo->id);
        }

        return response()->json($grupo, 201);
    }
    
    private function guardarGruposJorna(Array $data,int $idGrupo){
        $grupo_jornada = new AsignacionJornadaGrupo([
            'idJornada'=> $data['idJornada'],
            'idGrupo'=> $idGrupo
        ]);
        $grupo_jornada -> save();
    }

    private function guardarHorarioInfra(Array $data,int $idGrupo){
        $horarioInfraestructura = new HorarioInfraestructuraGrupo([
            'idGrupo' => $idGrupo,
            'idInfraestructura' => $data['idInfraestructura'],
            'fechaInicial'      => $data['fechaInicial'],
            'fechaFinal'        => $data['fechaFinal']
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
    public function show($id)
    {
        $dato = Grupo::with([
            'infraestructura' => function ($query) {
                $query->withPivot('fechaInicial', 'fechaFinal');
            },
            'gruposJornada',
            'participantes' => function ($query) {
                $query->withPivot('fechaInicial', 'fechaFinal', 'descripcion');
            }
        ])->find($id);

        if (!$dato) {
            return response()->json(['error' => 'El dato no fue encontrado'], 404);
        }

        return $dato->toJson();
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
            'idLider' => $data['idLider'],
            'idPrograma' => $data['idPrograma'],
            'idNivel' => $data['idNivel'],
            'idTipoFormacion' => $data['idTipoFormacion'],
            'idEstado' => $data['idEstado'],
            'idTipoOferta' => $data['idTipoOferta'],
        ]);

        // $grupos_jornada = $data['grupos_jornada'];
        // if($grupos_jornada){
        //     foreach ($grupos_jornada as $grupoJItem) {
        //         $this -> actualizarGruposJorna($grupoJItem, $grupo -> id);
        //     }
        // }else{
        //     AsignacionJornadaGrupo::where('idGrupo',$id) -> delete();
        // }
        
        foreach ($request->gruposJornada as $val) {
            foreach ($val as $val2) {
                $info = ['idGrupo' => $grupo->id, 'idInfraestructura' => $val2];
                $grupoJornada = new AsignacionJornadaGrupo($info);
                $grupoJornada->save();
            }
        }

        $infraestructura = $data['infraestructura'];
        if($infraestructura){
            foreach ($infraestructura as $horarioInfraItem) {
                $this -> actualizarHorarioInfra($horarioInfraItem,$grupo -> id);
            }
        }else{
            HorarioInfraestructuraGrupo::where('idGrupo',$id) -> delete();
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

        return response()->json($grupo, 200);
    }

    private function actualizarGruposJorna(Array $data,int $idGrupo){
        $grupo_jornada = AsignacionJornadaGrupo::find($data['id']);
        if($grupo_jornada){
            $grupo_jornada -> idJornada = $data['idJornada'];
            $grupo_jornada -> idGrupo = $idGrupo;

            $grupo_jornada -> save();
        }else{
            $this -> guardarGruposJorna($data,$idGrupo);
        }
        

    }

    private function actualizarHorarioInfra(Array $data,int $idGrupo){
        $horario_jornada = HorarioInfraestructuraGrupo::find($data['id']);
        if($horario_jornada){
            $horario_jornada -> idInfraestructura = $data['idInfraestructura'];
            $horario_jornada -> idGrupo = $idGrupo;
            $horario_jornada -> fechaInicial = $data['fechaInicial'];
            $horario_jornada -> fechaFinal = $data['fechaFinal'];

            $horario_jornada-> save();
        }else{
            $this -> guardarHorarioInfra($data,$idGrupo);
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
