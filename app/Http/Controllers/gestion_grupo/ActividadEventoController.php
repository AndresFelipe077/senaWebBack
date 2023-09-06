<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\ActividadEvento;
use Illuminate\Http\Request;

class ActividadEventoController extends Controller
{
    /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return response()->json(ActividadEvento::all(), 200);
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
    $ActividadEvento = new ActividadEvento($data);
    $ActividadEvento->save();

    return response()->json($ActividadEvento, 201);
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
