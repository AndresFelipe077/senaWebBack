<?php

namespace App\Http\Controllers;

use App\Models\AsignacionParticipante;
use App\Models\estadoParticipante;
use App\Models\Grupo;
use App\Models\Programa;
use App\Models\proyectoFormativo;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionParticipanteController extends Controller

{

  private $relations;

  public function __construct()
  {
    $this->relations = [
      'grupo',
      'grupo.infraestructuras',
      'grupo.infraestructuras.sede',
      'grupo.participantes',
      'grupo.jornadas.diaJornada',
      'usuario.persona',
      'tipoParticipacion',
      'estadoParticipantes',
    ];
  }

  public function index()
  {
    $data = AsignacionParticipante::with(['usuario.persona', 'grupo'])->get();
    return response()->json($data);
  }

  public function obtenerAsignacionesParticipantes()
  {
    $asignaciones = AsignacionParticipante::with('grupo')->get();

    $data = [];
    foreach ($asignaciones as $asignacion) {
      $grupo = $asignacion->grupo;
      if ($grupo) {
        $idPrograma = $grupo->idPrograma;
        $programa = Programa::find($idPrograma);
        $data[] = [
          'asignacionParticipantes' => $asignacion,
          'nombreGrupo' => $grupo->nombre,
          'nombrePrograma' => $programa ? $programa->nombrePrograma : null,
          'idPrograma' => $idPrograma,
          'programa' => $programa,
        ];
      }
    }

    return response()->json($data);
  }


  // public function obtenerAprendicesPorGrupo($idGrupo)
  // {
  //     $asignaciones = AsignacionParticipante::where('idGrupo', $idGrupo)
  //         ->whereIn('idEstadoParticipantes', ['ACTIVO', 'PENDIENTE'])
  //         ->with(['usuario'])
  //         ->get();

  //     return response()->json($asignaciones);
  // }


  public function obtenerAprendicesPorGrupo($idGrupo)
  {
      $latestIdsQuery = AsignacionParticipante::selectRaw('MAX(id) as latest_id')
          ->where('idGrupo', $idGrupo)
          ->groupBy('idParticipante');
  
      $aprendicesQuery = AsignacionParticipante::whereIn('id', function ($query) use ($latestIdsQuery) {
          $query->select('latest_id')
              ->fromSub($latestIdsQuery, 'subquery');
      })
          ->whereIn('idEstadoParticipantes', function ($query) {
              $query->select('id')
                  ->from('estadoParticipantes')
                  ->whereIn('detalleEstado', ['ACTIVO', 'PENDIENTE']);
          })
          ->whereNotIn('idParticipante', function ($query) {
              $query->select('idParticipante')
                  ->from('AsignacionParticipante')
                  ->where('idTipoParticipacion', 3)
                  ->groupBy('idParticipante');
          })
          ->with($this->relations);
      $ultimoLider = $this->getLastFichaByGroupIdAndType($idGrupo);
      if ($ultimoLider) {
          $datos = $aprendicesQuery->get()->concat([$ultimoLider->original]);
      } else {
          $datos = $aprendicesQuery->get();
      }
      return response()->json($datos);
  }
  
  public function crearHistorialDesdeRegistros()
  {
    try {
      DB::beginTransaction();

      $asignaciones = AsignacionParticipante::with(['estadoParticipantes:id,detalleEstado', 'tipoParticipacion:id,detalleParticipacion'])
        ->orderBy('id', 'asc')
        ->get(['id', 'observacion', 'fechaFinal', 'fechaInicial', 'idEstadoParticipantes', 'idTipoParticipacion']);

      $historialesPorId = [];

      foreach ($asignaciones as $asignacion) {
        // Obtener registros anteriores con el mismo idTipoParticipacion y ordenar por id
        $registrosAnteriores = AsignacionParticipante::where('idTipoParticipacion', $asignacion->idTipoParticipacion)
          ->where('id', '<', $asignacion->id)
          ->orderBy('id', 'asc')
          ->get();

        // Actualizar registros anteriores y agregar historial actual
        foreach ($registrosAnteriores as $registroAnterior) {
          $registroAnterior->fechaFinal = $asignacion->fechaInicial;
          $registroAnterior->idEstadoParticipantes = 2; // Cambiar estado a "inactivo"
          $historialAnterior = $historialesPorId[$registroAnterior->id][0];
          $historialAnterior['fechaFinal'] = $registroAnterior->fechaFinal;
          $historialAnterior['detalleEstado'] = 'INACTIVO';
          $historialesPorId[$registroAnterior->id][0] = $historialAnterior;

          $registroAnterior->save();
        }

        $historial = [
          'observacion' => $asignacion->observacion,
          'fechaFinal' => $asignacion->fechaFinal,
          'fechaInicial' => $asignacion->fechaInicial,
          'detalleEstado' => $asignacion->estadoParticipantes->detalleEstado,
          'detalleParticipacion' => $asignacion->tipoParticipacion->detalleParticipacion,
        ];

        $historialesPorId[$asignacion->id][] = $historial;
      }

      DB::commit();

      return response()->json(['historialesPorId' => $historialesPorId, 'message' => 'Historiales de asignación creados con éxito'], 200);
    } catch (\Exception $e) {
      DB::rollback();
      return response()->json(['message' => 'Ha ocurrido un error'], 500);
    }
  }

  /**
   * Assign an instructor to a participant ficha.
   *
   * This function receives the data necessary to create a participant assignment
   * to a group with a specific instructor.
   *
   * @author Andres Felipe Pizo Luligo
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function assignInstructorToFicha(Request $request): JsonResponse
  {
    $data = $request->all();

    $instructorAssign = new AsignacionParticipante([
      'idParticipante'        => $data['idParticipante'],
      'idGrupo'               => $data['idGrupo'],
      'idTipoParticipacion'   => $data['idTipoParticipacion'],
      'idEstadoParticipantes' => $data['idEstadoParticipantes'],
      'fechaInicial'          => $data['fechaInicial'],
      'fechaFinal'            => $data['fechaFinal'],
      'observacion'           => $data['observacion']
    ]);

    $instructorAssign->save();

    return response()->json($instructorAssign);
  }



  /**
   * Assign an aprendiz to a participant ficha.
   *
   * This function receives the data needed to create multi-trainee assignments
   * to a group with a specific participation type, status and dates.
   * 
   * @author vansss 'Vanesa Galindez' and 'Cristian Suarez' 
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */

  // Controlador de asignación de aprendices a fichas o grupos y cambia o asigna vocero y suplente 
  public function assignAprendizzToFicha(Request $request): JsonResponse
  {
    try {
      $data = $request->all();
      $idTipoParticipacion = $data['idTipoParticipacion'];
      $idGrupo = $data['idGrupo'];
      if ($data['idTipoParticipacion'] == 1 || $data['idTipoParticipacion'] == 2) {
        $ultimoRegistro = AsignacionParticipante::selectRaw('MAX(id) as latest_id')->groupBy('idParticipante');
        $ultimoRegistro = AsignacionParticipante::whereIn('id', function ($query) use ($ultimoRegistro, $idTipoParticipacion, $idGrupo) {
          $query->select('latest_id')
            ->fromSub($ultimoRegistro, 'subquery')
            ->where('idTipoParticipacion', '=', $idTipoParticipacion)
            ->where('idGrupo', '=', $idGrupo);
        })
          ->get();
        if ($ultimoRegistro->count() > 0) {
          $ultimoRegistro = $ultimoRegistro[0];
          $asignacion = new AsignacionParticipante();
          $asignacion->idParticipante = $ultimoRegistro->idParticipante;
          $asignacion->idGrupo = $ultimoRegistro->idGrupo;
          $asignacion->idTipoParticipacion = 4;
          $asignacion->idEstadoParticipantes = 1;
          $asignacion->fechaInicial = $ultimoRegistro->fechaInicial;
          $asignacion->fechaFinal = $ultimoRegistro->fechaFinal;
          $asignacion->observacion = $ultimoRegistro->observacion;
          $asignacion->save();
        }
      }
      $asignacionOriginal = new AsignacionParticipante();
      $asignacionOriginal->idParticipante = $data['idParticipante'];
      $asignacionOriginal->idGrupo = $data['idGrupo'];
      $asignacionOriginal->idTipoParticipacion = $data['idTipoParticipacion'];
      $asignacionOriginal->idEstadoParticipantes = $data['idEstadoParticipantes'];
      $asignacionOriginal->fechaInicial = $data['fechaInicial'];
      $asignacionOriginal->fechaFinal = $data['fechaFinal'];
      $asignacionOriginal->observacion = $data['observacion'];
      $asignacionOriginal->save();

      return response()->json(['message' => 'Asignación exitosa', 'asignacion' => $asignacionOriginal], 201);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Error al realizar la asignación', 'error' => $e->getMessage()], 500);
    }
  }
  /**
   * Update info of instructor in a ficha
   */
  public function updateInstructor($idAsignacionFicha)
  {
    // Buscar el registro por su ID
    $data = AsignacionParticipante::findOrFail($idAsignacionFicha);

    if ($data) {
      $data->fechaFinal = now();
      $data->idEstadoParticipantes = 3; // Trasladado
      $data->save();
    }

    return response()->json($data);
  }


  /**
   * Assign multiple learners to a participant card.
   *
   * This function receives the data needed to create multi-trainee assignments
   * to a group with a specific participation type, status and dates.
   *
   * @author Andres Felipe Pizo Luligo
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function assignAprendicesToFicha(Request $request)
  {
    $data = $request->all();

    $aprendicesIds = $data['aprendices'];

    $idGrupo = $data['idGrupo'];
    $idTipoParticipacion = $data['idTipoParticipacion'];
    $idEstadoParticipantes = $data['idEstadoParticipantes'];
    $fechaInicial = $data['fechaInicial'];
    $fechaFinal = $data['fechaFinal'];
    $observacion = $data['observacion'];

    $aprendicesAssignments = [];

    foreach ($aprendicesIds as $aprendizId) {
      $aprendicesAssign = new AsignacionParticipante([
        'idParticipante' => $aprendizId,
        'idGrupo' => $idGrupo,
        'idTipoParticipacion' => $idTipoParticipacion,
        'idEstadoParticipantes' => $idEstadoParticipantes,
        'fechaInicial' => $fechaInicial,
        'fechaFinal' => $fechaFinal,
        'observacion' => $observacion
      ]);

      $aprendicesAssign->save();
      $aprendicesAssignments[] = $aprendicesAssign;
    }

    return response()->json($aprendicesAssignments);
  }

  /**
   * Obtain the participant assignments in which the instructor acts as a leader.
   *
   * @param int $idInstructor The ID of the instructor whose assignments will be searched.
   * @return \Illuminate\Http\JsonResponse A JSON response containing the participant mappings.
   * @author Andres Felipe Pizo Luligo
   */
  public function getFichasByInstructorLider($idInstructor): JsonResponse
  {

    $fichasByInstructor = AsignacionParticipante::where('idParticipante', $idInstructor)
      ->where('idTipoParticipacion', 3)
      ->with($this->relations)->get();

    return response()->json($fichasByInstructor);
  }


  /**
   * Get fichas by instructor
   * @param int $idFicha
   * @author Andres Felipe Pizo Luligo
   */
  public function getFichasById($idFicha): JsonResponse
  {
    $fichasByInstructor = AsignacionParticipante::where('idGrupo', $idFicha)
      ->where('idTipoParticipacion', 3)
      ->with($this->relations)->get();

    return response()->json($fichasByInstructor);
  }

  /**
   * Get last ficha by id
   * @param int $idLastFicha
   * @author Andres Felipe Pizo Luligo
   */
  public function getLastFichaByGroupIdAndType($idGrupo): JsonResponse
  {
    $ultimaFicha = AsignacionParticipante::where('idGrupo', $idGrupo)
      ->where('idTipoParticipacion', 3)
      ->latest('created_at')
      ->with($this->relations)
      ->first();

    return response()->json($ultimaFicha);
  }

  /**
   * Get last register by idParticipante
   * @param int $idParticipante
   * @author Andres Felipe Pizo Luligo
   */
  public function getLastRegisterByIdParticipante($idParticipante): JsonResponse
  {
    $lastRegister = AsignacionParticipante::where('idParticipante', $idParticipante)
      ->latest('created_at')
      ->first();
    return response()->json($lastRegister);
  }

  public function getLastRegisterOfAllParticipants(): JsonResponse
  {
    $asignaciones = AsignacionParticipante::selectRaw('MAX(id) as latest_id')
      ->groupBy('idParticipante');

    $asignaciones = AsignacionParticipante::whereIn('id', function ($query) use ($asignaciones) {
      $query->select('latest_id')
        ->fromSub($asignaciones, 'subquery');
    })
      ->whereIn('idEstadoParticipantes', function ($query) {
        $query->select('id')
          ->from('estadoParticipantes');
      })
      ->whereNotIn('idTipoParticipacion', [3])
      ->with($this->relations)
      ->get();

    return response()->json($asignaciones);
  }
  public function getEstadosParticipantes(): JsonResponse
  {
    $estadosExcluidos = ['ACTIVO', 'PENDIENTE'];

    $estadosParticipantes = EstadoParticipante::whereNotIn('detalleEstado', $estadosExcluidos)
      ->get();

    return response()->json($estadosParticipantes);
  }

  
  public function getHistorialAprendices($idGrupo): JsonResponse
{
    $registros = AsignacionParticipante::where('idGrupo', $idGrupo)
        ->orderBy('id', 'asc') 
        ->whereNotIn('idTipoParticipacion', [3])
        ->with($this->relations)->get();

    return response()->json($registros);
  }
}
