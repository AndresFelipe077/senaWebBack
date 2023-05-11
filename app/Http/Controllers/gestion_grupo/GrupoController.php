<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\ActivationCompanyUser;
use App\Models\AsignacionJornadaGrupo;
use App\Models\AsignacionParticipante;
use App\Models\Grupo;
use App\Models\HorarioInfraestructuraGrupo;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'infraestructura' => function ($query) {
                $query->withPivot('fechaInicial', 'fechaFinal'); // Mostrara los datos de la infraestructura
            },                                                   // y ademas los campos restantes de la tabla intermedia
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

        if ($infraestructura) {
            $grupos = $grupos->map(function ($grupo) {
                $infraestructura = $grupo->infraestructura->map(function ($infra) {
                    $pivot = $infra->pivot;
                    unset($infra->pivot); // eliminar el objeto pivot para evitar redundancia
                    return [
                        'idInfraestructura' => $infra->idInfraestructura,
                        'nombreInfraestructura' => $infra->nombreInfraestructura,
                        'fechaInicial' => $pivot->fechaInicial, // incluir campos adicionales
                        'fechaFinal' => $pivot->fechaFinal, // incluir campos adicionales
                    ];
                });

                $grupo->infraestructura = $infraestructura;
                return $grupo;
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

        // $grupos = Grupo::query();
        if ($gruposJornada) {
            $grupos->whereHas('gruposJornada', function ($q) use ($gruposJornada) {
                return $q->select('id')
                    ->where('id', $gruposJornada)
                    ->orWhere('nombreJornada', $gruposJornada);
            })->with('jornadas');
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


        // foreach ($request->grupos_jornada as $val) {
        //     foreach ($val as $val2) {
        //         $info = ['idGrupo' => $grupo->id, 'idJornada' => $val2];
        //         $asignacionJornada = new AsignacionJornadaGrupo($info);
        //         $asignacionJornada->save();
        //     }
        // }

        // foreach ($request->infraestructura as $val) {
        //     foreach ($val as $val2) {
        //         $info = ['idGrupo' => $grupo->id, 'idInfraestructura' => $val2];
        //         $horarioInfraestructura = new HorarioInfraestructuraGrupo($info);
        //         $horarioInfraestructura->save();
        //     }
        // }


        $data = $request->all();

        $infraestructura = $data['infraestructura'];

        // dd($infraestructura);

       foreach ($infraestructura as $infraItem) {

        // var_dump($infraItem);

        $this->guardarHorarioInfra($infraItem, $grupo->id);
        
            // foreach ($val as $val2) {
            //     $info = [
            //         'idGrupo' => $grupo->id,
            //         'idInfraestructura' => $val2,
            //         'fechaInicial' => $request->fechaInicial,
            //         'fechaFinal' => $request->fechaFinal
            //     ];
            //     $horarioInfraestructura = new HorarioInfraestructuraGrupo($info);
            //     $horarioInfraestructura->save();
            // }
        }
       

        // if (isset($request->participantes)) {
        //     foreach ($request->participantes as $val) {
        //         foreach ($val as $val2) {
        //             $info = ['idGrupo' => $grupo->id, 'idParticipante' => $val2];
        //             $participante = new AsignacionParticipante($info);
        //             $participante->save();
        //         }
        //     }
        // }

        return response()->json($grupo, 201);
    }

    private function guardarHorarioInfra(Array $data,int $idGrupo){
        var_dump($data);
        $horarioInfraestructura = new HorarioInfraestructuraGrupo([
            'idGrupo' => $idGrupo,
            'idInfraestructura' => $data['idInfraestructura'],
            'fechaInicial'      => $data['fechaInicial'],
            'fechaFinal'        => $data['fechaFinal']
        ]);
        // var_dump($horarioInfraestructura);
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

        // Eliminar todas las asignaciones de infraestructura previas
        HorarioInfraestructuraGrupo::where('idGrupo', $id)->delete();

        // Eliminar todas las asignaciones de jornada previas
        AsignacionJornadaGrupo::where('idGrupo', $id)->delete();


        foreach ($request->grupos_jornada as $val) {
            foreach ($val as $val2) {
                $info = ['idGrupo' => $grupo->id, 'idJornada' => $val2];
                $asignacionJornada = new AsignacionJornadaGrupo($info);
                $asignacionJornada->save();
            }
        }

        // foreach ($request->infraestructura as $val) {
        //     foreach ($val as $val2) {
        //         $info = ['idGrupo' => $grupo->id, 'idInfraestructura' => $val2];
        //         $horarioInfraestructura = new HorarioInfraestructuraGrupo($info);
        //         $horarioInfraestructura->save();
        //     }
        // }

        // foreach ($data['infraestructura'] as $horarioInfraestructura) {
        //     $info = [
        //         'idGrupo' => $grupo->id,
        //         'idInfraestructura' => $horarioInfraestructura['idInfraestructura'],
        //         'fechaInicial'      => $horarioInfraestructura,
        //         'fechaFinal'        => $horarioInfraestructura
        //     ];
        //     $horarioInfraestructura = new HorarioInfraestructuraGrupo($info);
        //     $horarioInfraestructura->save();
        // }


        foreach ($request->infraestructura as $val) {
            var_dump($request);
            foreach ($val as $val2) {
                $info = [
                    'idGrupo' => $grupo->id,
                    'idInfraestructura' => $val2->idInfraestructura,
                    'fechaInicialGrupo' => $request->fechaInicialGrupo,
                    'fechaFinalGrupo' => $request->fechaFinalGrupo
                ];
                $horarioInfraestructura = new HorarioInfraestructuraGrupo($info);
                $horarioInfraestructura->update();

            }
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
