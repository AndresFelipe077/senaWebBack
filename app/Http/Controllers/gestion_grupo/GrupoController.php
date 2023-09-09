<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\AsignacionJornadaGrupo;
use App\Models\Competencias;
use App\Models\Grupo;
use App\Models\HorarioInfraestructuraGrupo;
use App\Models\Programa;
use App\Models\proyectoFormativo;
use App\Models\resultadoAprendizaje;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GrupoController extends Controller
{
  private $relations;

  public function __construct()
  {
    $this->relations = [
      'tipoGrupo',
      'proyectoFormativo.programas.tipoPrograma',
      'tipoFormacion',
      'estadoGrupo',
      'tipoOferta',
      'jornadas.diaJornada',
      'participantes',
      'infraestructuras',
      'infraestructuras.sede'
    ];
  }
  /**
   * Listar todos los grupos con sus relaciones
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {

    $grupos = Grupo::with($this->relations)->get();

    //quitar pivots
    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      $grupo['participantes'] = $grupo['participantes']->map(function ($participante) {
        $pivot = $participante['pivot'];
        unset($participante['pivot']);
        $participante['participantes_asignados'] = $pivot;
        return $participante;
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
  }

  public function getGruposByEspecial()
  {
    $grupos = Grupo::with($this->relations)->where('idTipoGrupo', '2')->get();

    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      $grupo['participantes'] = $grupo['participantes']->map(function ($participante) {
        $pivot = $participante['pivot'];
        unset($participante['pivot']);
        $participante['participantes_asignados'] = $pivot;
        return $participante;
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
  }

  public function getGruposByFicha()
  {
    $grupos = Grupo::with($this->relations)->where('idTipoGrupo', '1')->get();

    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      $grupo['participantes'] = $grupo['participantes']->map(function ($participante) {
        $pivot = $participante['pivot'];
        unset($participante['pivot']);
        $participante['participantes_asignados'] = $pivot;
        return $participante;
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
  }

  /**
   * Crear grupo con sus relaciones
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function store(Request $request)
  {
    $data = $request->all();

    $existingGrupo = Grupo::where('nombre', $data['nombre'])->first();
    if ($existingGrupo) {
      return response()->json(['error' => 'Número de grupo existente!!!.'], 422);
    }

    $grupo = new Grupo([
      'nombre'              => $data['nombre'],
      'fechaInicialGrupo'   => $data['fechaInicialGrupo'],
      'fechaFinalGrupo'     => $data['fechaFinalGrupo'],
      'observacion'         => $data['observacion'],
      'idTipoGrupo'         => $data['idTipoGrupo'],
      'idProyectoFormativo' => $data['idProyectoFormativo'],
      'idTipoFormacion'     => $data['idTipoFormacion'],
      'idEstado'            => $data['idEstado'],
      'idTipoOferta'        => $data['idTipoOferta']
    ]);

    if ($request->hasFile('imagenIcon')) {

      $cadena = $request->file('imagenIcon')->getClientOriginalName();
      $cadenaConvert = str_replace(" ", "_", $cadena);
      $nombre = Str::random(10) . '_' . $cadenaConvert;
      $rutaAlmacenamiento = 'imagenes/especial' . $nombre;
      $request->file('imagenIcon')->storeAs('public', $rutaAlmacenamiento);

      $grupo->imagenIcon = $rutaAlmacenamiento;
    }

    $grupo->save();

    /*$infraestructuras = $data['infraestructuras'];

    foreach ($infraestructuras as $infraItem) {

      $existeAsignacion = $this->verificarAsignacionInfraestructura($data['infraestructuras'], $data['jornadas']);

      if ($existeAsignacion) {
        return response()->json(['error' => 'Infraestructura ocupada en la misma jornada.'], 422);
      } else {
        $this->guardarHorarioInfra($infraItem, $grupo->id);
      }
    }


    foreach ($request->jornadas as $jornadaItem) {
      foreach ($jornadaItem as $jItem) {
        $info = ['idGrupo' => $grupo->id, 'idJornada' => $jItem];
        $asignacionJornadaGrupo = new AsignacionJornadaGrupo($info);
        $asignacionJornadaGrupo->save();
      }
    }*/

    $resultadoAprendizaje = $this->createConfiguracionRapByGrupo($grupo->id);

    if ($resultadoAprendizaje) {
      return response()->json($resultadoAprendizaje);
    }

    $grupo = Grupo::with($this->relations)->findOrFail($grupo->id);


    return response()->json($grupo, 201);
  }


  /**
   * Mostrar un grupo por su Id
   *
   * @param  \App\Models$grupo  $grupo
   * @return \Illuminate\Http\Response
   */
  public function show(int $id)
  {
    $dato = Grupo::with($this->relations)->find($id);

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

  public function showByIdInfra(int $id)
  {

    $grupos = Grupo::whereHas('infraestructuras', function ($query) use ($id) {
      $query->where('idInfraestructura', $id);
    })->with($this->relations)->get();

    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      return $grupo;
    });

    return response()->json($newGrupos);
  }

  public function showByIdSede(int $id)
  {

    $grupos = Grupo::whereHas('infraestructuras', function ($query) use ($id) {
      $query->where('idSede', $id);
    })->with($this->relations)->get();

    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      return $grupo;
    });

    return response()->json($newGrupos);
  }


  /**
   * Actualizar estado de las infraestructuras y crear una nueva infraestructura con su grupo cuando se afecte esta
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models$grupo  $grupo
   * @return \Illuminate\Http\Response
   */

  public function update(Request $request, $id)
  {
    $data = $request->all();
    $grupo = Grupo::with($this->relations)->findOrFail($id);

    // Validar infraestructuras y jornadas
    $existeAsignacion = $this->verificarAsignacionInfraestructuraUpdate($data['infraestructuras'], $request->jornadas, $grupo->id);

    if ($existeAsignacion) {
      return response()->json(['error' => 'Infraestructura ocupada en la misma jornada.'], 400);
    }

    $grupo->update([
      'nombre' => $data['nombre'],
      'fechaInicialGrupo' => $data['fechaInicialGrupo'],
      'fechaFinalGrupo' => $data['fechaFinalGrupo'],
      'observacion' => $data['observacion'],
      'idTipoGrupo' => $data['idTipoGrupo'],
      'idProyectoFormativo' => $data['idProyectoFormativo'],
      'idTipoFormacion' => $data['idTipoFormacion'],
      'idEstado' => $data['idEstado'],
      'idTipoOferta' => $data['idTipoOferta'],
    ]);

    $currentInfraestructuras = $grupo->infraestructuras()->whereDate('fechaFinal', '>=', now())->pluck('idInfraestructura');
    $grupo->infraestructuras()->detach($currentInfraestructuras); // No poder eliminar grupos que ya pasaron su fecha final

    $infraestructura = $data['infraestructuras'];

    foreach ($infraestructura as $infraItem) { // Guardar infraestructura actualizada
      $this->guardarHorarioInfra($infraItem, $grupo->id);
    }

    AsignacionJornadaGrupo::where('idGrupo', $grupo->id)->delete();

    foreach ($request->jornadas as $jornaItem) {
      foreach ($jornaItem as $jItem) {
        $info = ['idGrupo' => $grupo->id, 'idJornada' => $jItem];
        $asignacionJornadaGrupo = new AsignacionJornadaGrupo($info);
        $asignacionJornadaGrupo->save();
      }
    }

    $grupo->save(); // Guardar el grupo actualizado

    return response()->json($grupo, 200);
  }


  /**
   * Eliminar el grupo con sus relaciones
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

  /**
   * Guarda el horario de infraestructura para un grupo.
   *
   * @param array $data Los datos del horario de infraestructura.
   * @param int $idGrupo El ID del grupo al que se asignará el horario de infraestructura.
   * @return void
   */
  private function guardarHorarioInfra(array $data, int $idGrupo)
  {

    $fechaInicial = $data['horario_infraestructura']['fechaInicial'];
    $fechaFinal = $data['horario_infraestructura']['fechaFinal'];

    if (strtotime($fechaFinal) < strtotime('today')) {
      // Si la "fechaFinal" ya ha pasado, no se guarda por infraestructuras que han terminado.
      return; // No se guarda la infraestructura y se sale de la función.
    }

    // Verificar si existe una infraestructura anterior no actualizada
    $existingInfraestructura = HorarioInfraestructuraGrupo::where('idGrupo', $idGrupo)
      ->where('idEstado', '<>', 5) // Excluir infraestructuras ya actualizadas
      ->first();

    if ($existingInfraestructura) {
      $existingInfraestructura->update(['idEstado' => 5]); // 5 = ACTUALIZADO
    }

    $estadoId = ($fechaInicial > now()) ? 2 : 1; // 2 = PENDIENTE, 1 = EN CURSO

    // Crear la nueva infraestructura
    $horarioInfraestructura = new HorarioInfraestructuraGrupo([
      'idGrupo' => $idGrupo,
      'idInfraestructura' => $data['horario_infraestructura']['idInfraestructura'],
      'fechaInicial' => $fechaInicial,
      'fechaFinal' => $fechaFinal,
      'idEstado' => $estadoId
    ]);

    $horarioInfraestructura->save();
  }


  /**
   * Actualiza el estado de las infraestructuras de un grupo de "PENDIENTE" a "EN CURSO".
   *
   * @param int $idGrupo El ID del grupo.
   * @return void
   */
  private function infraestructuraEnCurso($idGrupo)
  {
    HorarioInfraestructuraGrupo::where('idGrupo', $idGrupo)
      ->where('idEstado', 2) // ID 2 representa el estado "PENDIENTE"
      ->where('fechaInicial', '<=', now())
      ->where('fechaFinal', '>', now()) // Fecha final mayor que la fecha actual
      ->update(['idEstado' => 1]); // Actualiza el campo idEstado a 1 (EN CURSO)
  }


  /**
   * Verifica si hay asignación de infraestructuras en las jornadas y horarios especificados.
   *
   * @param array $infraestructuras Un array de infraestructuras a verificar.
   * @param array $jornadas Un array de jornadas a considerar.
   * @return bool Devuelve true si existe una asignación de infraestructuras en las jornadas y horarios especificados, de lo contrario devuelve false.
   */
  private function verificarAsignacionInfraestructura($infraestructuras, $jornadas)
  {
    foreach ($infraestructuras as $infraestructura) {
      $infraestructuraId = $infraestructura['horario_infraestructura']['idInfraestructura'];
      $fechaInicial = $infraestructura['horario_infraestructura']['fechaInicial'];
      $fechaFinal = $infraestructura['horario_infraestructura']['fechaFinal'];

      $existeAsignacion = HorarioInfraestructuraGrupo::where('idInfraestructura', $infraestructuraId)
        ->where(function ($query) use ($fechaInicial, $fechaFinal, $jornadas) {
          $query->where(function ($query) use ($fechaInicial, $fechaFinal) {
            $query->where('fechaInicial', '<=', $fechaFinal)
              ->where('fechaFinal', '>=', $fechaInicial);
          })
            ->whereHas('grupo.jornadas', function ($query) use ($jornadas) {
              $query->whereIn('jornada.id', $jornadas);
            });
        })
        ->exists();

      if ($existeAsignacion) {
        return true;
      }
    }

    return false;
  }


  /**
   * Verifica si existe alguna asignación de infraestructura en conflicto para la actualización del grupo.
   *
   * @param array $infraestructuras Un arreglo de infraestructuras.
   * @param array $jornadas Un arreglo de jornadas.
   * @param int $grupoId El ID del grupo que se está actualizando.
   * @return bool Devuelve true si existe una asignación en conflicto, de lo contrario, devuelve false.
   */
  private function verificarAsignacionInfraestructuraUpdate($infraestructuras, $jornadas, $grupoId)
  {
    // Verificar si no se han realizado cambios en las infraestructuras y jornadas
    if (empty($infraestructuras) && empty($jornadas)) {
      return false;
    }

    foreach ($infraestructuras as $infraestructura) {
      $infraestructuraId = $infraestructura['horario_infraestructura']['idInfraestructura'];
      $fechaInicial = $infraestructura['horario_infraestructura']['fechaInicial'];
      $fechaFinal = $infraestructura['horario_infraestructura']['fechaFinal'];

      $existeAsignacion = HorarioInfraestructuraGrupo::where('idInfraestructura', $infraestructuraId)
        ->where(function ($query) use ($fechaInicial, $fechaFinal, $jornadas) {
          $query->where(function ($query) use ($fechaInicial, $fechaFinal) {
            $query->where('fechaInicial', '<=', $fechaFinal)
              ->where('fechaFinal', '>=', $fechaInicial);
          })
            ->whereHas('grupo.jornadas', function ($query) use ($jornadas) {
              $query->whereIn('jornada.id', $jornadas);
            });
        })
        ->where('idGrupo', '<>', $grupoId) // Excluir el grupo que se está actualizando
        ->exists();

      if ($existeAsignacion) {
        return true;
      }
    }

    return false;
  }


  //   public function showByIdProyectoFor($programaId)
  //   {
  //       $grupos = Grupo::whereHas('proyectoFormativo', function ($query) use ($programaId) {
  //           $query->where('idPrograma', $programaId);
  //       })->get();

  //       return response()->json($grupos);
  //   }

  public function showByIdProyectoFor(int $id)
  {
    $grupos = Grupo::with($this->relations)
      ->where('idProyectoFormativo', $id)->get();
    return response()->json($grupos);
  }


  /**
   * Get Fichas depending by parameter
   *
   * @param String $nombreTipoGrupo
   * @return \Illuminate\Http\JsonResponse A JSON response containing the participant mappings.
   * @author Andres Felipe Pizo Luligo
   */
  public function getTipoGrupoByParameter($nombreTipoGrupo)
  {
    $grupo = Grupo::whereHas('tipoGrupo', function ($query) use ($nombreTipoGrupo) {
      $query->where('nombreTipoGrupo', $nombreTipoGrupo);
    })->get();

    return $grupo;
  }


  /*private function createConfiguracionRapByGrupo($idFicha)
  {
    $ficha = Grupo::find($idFicha);

    if (!$ficha) {
      return response()->json(['message' => 'Ficha not found']);
    }

    // Proyecto Formativo
    $idProyectoFormativo = $ficha->proyectoFormativo->id;
    $numeroTotalRaps = $ficha->proyectoFormativo->numeroTotalRaps;


    // Programa
    $programa = proyectoFormativo::find($idProyectoFormativo);
    $idPrograma = $programa->programas->id;

    // Competencias
    $competencia = Programa::find($idPrograma);
    $idCompetencia = $competencia->competencias->id;

    // Resultados de aprendizaje
    $resultadoAprendizaje = Competencias::find($idCompetencia);
    $idResultadoAprendizaje = $resultadoAprendizaje->resultadosAprendizaje->id;

    $resultadoAprendizaje = resultadoAprendizaje::findOrFail($idResultadoAprendizaje);

    return response()->json($resultadoAprendizaje);
  }*/
  private function createConfiguracionRapByGrupo($idFicha)
  {
    $ficha = Grupo::find($idFicha);

    if (!$ficha) {
      return response()->json(['message' => 'Ficha not found']);
    }

    // Proyecto Formativo
    $idProyectoFormativo = $ficha->proyectoFormativo->id;
    $numeroTotalRaps = $ficha->proyectoFormativo->numeroTotalRaps;

    // Programa
    $idPrograma = $ficha->proyectoFormativo->idPrograma;

    // Competencias
    $programa = Programa::find($idPrograma);
    $idCompetencia = $programa->idCompetencia;

    // Resultados de aprendizaje
    $competencia = Competencias::find($idCompetencia);

    /*if (!$competencia) {
      return response()->json(["message" => "Competencia not found"]);
    }*/

    // $resultadosAprendizaje = $competencia->resultadosAprendizaje;

    // Devolver todos los objetos de ResultadoAprendizaje en formato JSON
    return response()->json([
      'idProyectoFormativo' => $idProyectoFormativo,
      'numeroTotalRaps' => $numeroTotalRaps,
      'idPrograma' => $idPrograma,
      'idCompetencia' => $idCompetencia,
      // 'resultadosAprendizaje' => $resultadosAprendizaje,
    ]);
  }
}
