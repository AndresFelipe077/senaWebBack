<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\TipoGrupo;
use Illuminate\Http\Request;

class TipoGrupoController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return response()->json(TipoGrupo::all(), 200);
  }

  public function getTipoGrupoFicha()
  {
    return TipoGrupo::where('nombreTipoGrupo', 'FICHA')->get();
  }

  public function getTipoGrupoEspecial()
  {
    return TipoGrupo::where('nombreTipoGrupo', 'ESPECIAL')->get();
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
    $tipoGrupo = new TipoGrupo($data);
    $tipoGrupo->save();

    return response()->json($tipoGrupo, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\TipoGrupo  $tipoGrupo
   * @return \Illuminate\Http\Response
   */
  public function show(int $tipoGrupo)
  {
    $tipoGrupo = TipoGrupo::find($tipoGrupo);
    return response()->json($tipoGrupo);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\TipoGrupo  $tipoGrupo
   * @return \Illuminate\Http\Response
   */

  public function update(Request $request, int $id)
  {
    $data = $request->all();
    $tipoGrupo = TipoGrupo::findOrFail($id);
    $tipoGrupo->fill($data);
    $tipoGrupo->save();

    return response()->json($tipoGrupo);
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\TipoGrupo  $tipoGrupo
   * @return \Illuminate\Http\Response
   */
  public function destroy(int $id)
  {
    $tipoGrupo = TipoGrupo::findOrFail($id);
    $result = $tipoGrupo->delete();
    if ($result) {
      return ["result" => "delete success"];
    } else {
      return ["result" => "delete failed"];
    }
  }
}
