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

      // Verificar si el grupo es válido antes de acceder a la propiedad idPrograma
      if ($grupo) {
        $idPrograma = $grupo->idPrograma;

        // Obtener todos los detalles del programa
        $programa = Programa::find($idPrograma);

        // Agregar los datos necesarios al arreglo
        $data[] = [
          'asignacionParticipantes' => $asignacion,
          'nombreGrupo' => $grupo->nombre,
          'nombrePrograma' => $programa ? $programa->nombrePrograma : null, // Verificar si $programa es válido
          'idPrograma' => $idPrograma,
          'programa' => $programa, // Agregar el programa completo
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

    // $usuariosActivados = ActivationCompanyUser::role('APRENDIZ')->active()->get();

    $asignaciones = AsignacionParticipante::where('idGrupo', $idGrupo)
      ->whereIn('idEstadoParticipantes', function ($query) {
        $query->select('id')
          ->from('estadoParticipantes')
          ->whereIn('detalleEstado', ['ACTIVO', 'PENDIENTE']);
      })
      ->with(['usuario.persona'])
      ->get();

    return response()->json($asignaciones);
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
   * @author vansss 'Vanesa Galindez'
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */

  // Controlador de asignación de aprendices a fichas o grupos
public function assignAprendizzToFicha(Request $request): JsonResponse
{
    try {
        $data = $request->all();
        $asignacion = new AsignacionParticipante();
        $asignacion->idParticipante = $data['idParticipante'];
        $asignacion->idGrupo = $data['idGrupo'] ?? null;
        $asignacion->idTipoParticipacion = 4; // 4 para participacion aprendiz
        $asignacion->idEstadoParticipantes = 1; // 1 para estado activo
        $asignacion->fechaInicial = null; // aun no establecida
        $asignacion->fechaFinal = null; // aun no establecida
        $asignacion->observacion = $data['observacion'];
        $asignacion->save();
        return response()->json(['message' => 'Asignación exitosa', 'asignacion' => $asignacion], 201);
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
      ->with($this->relations)->get();

    return response()->json($fichasByInstructor);
  }

  /**
   * Get last ficha by id
   * @param int $idLastFicha
   * @author Andres Felipe Pizo Luligo
   */
  public function getLastFichaById($idLastFicha): JsonResponse
  {
    $ultimaFicha = AsignacionParticipante::where('idGrupo', $idLastFicha)
      ->orderBy('created_at', 'desc')
      ->first();
    return response()->json($ultimaFicha);
  }
}
