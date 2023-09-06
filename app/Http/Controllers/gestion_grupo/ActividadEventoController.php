<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\ActividadEvento;
use App\Models\AsignacionJornadaActividadEvento;
use Illuminate\Http\Request;

class ActividadEventoController extends Controller
{


  private $relations;

  public function __construct()
  {
    $this->relations = [
      'infraestructura',
      'jornadas.diaJornada',
      'participantes',
    ];
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $ActividadEventos = ActividadEvento::with($this->relations)->get();

    //quitar pivots
    $newActividadEventos = $ActividadEventos->map(function ($actividadEvento) {

      $actividadEvento['jornadas'] = $actividadEvento['jornadas']->map(function ($jornada) {
        $pivot = $jornada['pivot'];
        unset($jornada['pivot']);
        $jornada['jornada_actividad_evento'] = $pivot;
        return $jornada;
      });
      return $actividadEvento;
    });
    return response()->json($newActividadEventos, 200);
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

    // Verificar si esta el array pero vacio si es asi, retorna not data
    if (isset($data['participantes']) && is_array($data['participantes']) && empty($data['participantes'])) {
      return response()->json(['message' => 'participantes not have data'], 500);
    }

    // Verificar si se proporcionó un array de IDs en 'participantes'
    if (isset($data['participantes']) && is_array($data['participantes'])) {
      $actividades = [];

      // Obtener datos comunes para todos los registros
      $idInfraestructura = $data['idInfraestructura'];
      $observacion = $data['observacion'];
      $fechaInicial = $data['fechaInicial'];
      $fechaFinal = $data['fechaFinal'];

      foreach ($data['participantes'] as $idParticipante) {
        $actividad = new ActividadEvento([
          'idParticipante'    => $idParticipante,
          'idInfraestructura' => $idInfraestructura,
          'observacion'       => $observacion,
          'fechaInicial'      => $fechaInicial,
          'fechaFinal'        => $fechaFinal,
        ]);

        $actividad->save();

        foreach ($request->jornadas as $jornadaItem) {
          foreach ($jornadaItem as $jItem) {
            $info = ['idActividadEvento' => $actividad->id, 'idJornada' => $jItem];
            $asignacionJornadaactividadEvento = new AsignacionJornadaActividadEvento($info);
            $asignacionJornadaactividadEvento->save();
          }
        }

        $actividades[] = $actividad;
      }

      $actividades = ActividadEvento::with($this->relations)->findOrFail($actividad->id);

      return response()->json($actividades, 201);
    } else {
      // Si no es un array, crear un solo registro normal
      $actividad = new ActividadEvento($data);
      $actividad->save();

      foreach ($request->jornadas as $jornadaItem) {
        foreach ($jornadaItem as $jItem) {
          $info = ['idActividadEvento' => $actividad->id, 'idJornada' => $jItem];
          $asignacionJornadaactividadEvento = new AsignacionJornadaActividadEvento($info);
          $asignacionJornadaactividadEvento->save();
        }
      }

      $actividad = ActividadEvento::with($this->relations)->findOrFail($actividad->id);

      return response()->json($actividad, 201);
    }
  }


  /**
   * Display the specified resource.
   *
   * @param  \App\Models\ActividadEvento  $ActividadEvento
   * @return \Illuminate\Http\Response
   */
  public function show(int $ActividadEvento)
  {
    $ActividadEvento = ActividadEvento::find($ActividadEvento);
    return response()->json($ActividadEvento);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\ActividadEvento  $ActividadEvento
   * @return \Illuminate\Http\Response
   */

  public function update(Request $request, int $id)
  {
    $data = $request->all();
    $actividadEvento = ActividadEvento::findOrFail($id);
    $actividadEvento->fill($data);
    $actividadEvento->save();

    AsignacionJornadaActividadEvento::where('idActividadEvento', $actividadEvento->id)->delete();

    if ($request->jornadas) {
      foreach ($request->jornadas as $jornadaItem) {
        foreach ($jornadaItem as $jItem) {
          $info = ['idActividadEvento' => $actividadEvento->id, 'idJornada' => $jItem];
          $asignacionJornadaactividadEvento = new AsignacionJornadaActividadEvento($info);
          $asignacionJornadaactividadEvento->save();
        }
      }
    }

    return response()->json($actividadEvento);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\ActividadEvento  $ActividadEvento
   * @return \Illuminate\Http\Response
   */
  public function destroy(int $id)
  {
    $ActividadEvento = ActividadEvento::findOrFail($id);
    $result = $ActividadEvento->delete();
    if ($result) {
      return ["result" => "delete success"];
    } else {
      return ["result" => "delete failed"];
    }
  }
}
