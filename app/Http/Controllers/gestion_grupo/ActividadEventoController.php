<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\ActividadEvento;
use Illuminate\Http\Request;

class ActividadEventoController extends Controller
{


  private $relations;

  public function __construct()
  {
    $this->relations = [
      'infraestructura',
      'jornada.diaJornada',
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
    return response()->json(ActividadEvento::with($this->relations)->get(), 200);
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

    // Verificar si esta el array pero vacio si es asi retorna not data
    if(isset($data['participantes']) && is_array($data['participantes']) && empty($data['participantes']))
    {
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
      $idJornada = $data['idJornada'];

      foreach ($data['participantes'] as $idParticipante) {
        $actividad = new ActividadEvento([
          'idParticipante'    => $idParticipante,
          'idInfraestructura' => $idInfraestructura,
          'observacion'       => $observacion,
          'fechaInicial'      => $fechaInicial,
          'fechaFinal'        => $fechaFinal,
          'idJornada'         => $idJornada
        ]);

        $actividad->save();
        $actividades[] = $actividad;
      }

      return response()->json($actividades, 201);
    } else {
      // Si no es un array, crear un solo registro normal
      $actividad = new ActividadEvento($data);
      $actividad->save();

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
    $ActividadEvento = ActividadEvento::findOrFail($id);
    $ActividadEvento->fill($data);
    $ActividadEvento->save();

    return response()->json($ActividadEvento);
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
