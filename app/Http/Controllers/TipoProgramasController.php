<?php

namespace App\Http\Controllers;

use App\Models\TipoProgramas;
use Illuminate\Http\Request;

class TipoProgramasController extends Controller
{

    public function index(Request $request)
    {
        $nombreTipoPrograma = $request->input('nombreTipoPrograma');

        $tipoProgramas = TipoProgramas::query();
        if($nombreTipoPrograma){
            $tipoProgramas->where('nombreTipoPrograma', $nombreTipoPrograma);
        }
        return response()->json($tipoProgramas->get());
    }


    public function store(Request $request)
    {
        $data=$request->all();
        $tipoPrograma = new TipoProgramas($data);
        $tipoPrograma->save();

        return response()->json($tipoPrograma,201);
    }


    public function show(int $id)
    {
        $tipoPrograma = TipoProgramas::find($id);

        return response()->json($tipoPrograma,200);
    }


    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $tipoPrograma = TipoProgramas::findOrFail($id);
        $tipoPrograma->fill($data);
        $tipoPrograma->save();

        return response()->json($tipoPrograma);
    }


    public function destroy(int $id)
    {
        $tipoPrograma = TipoProgramas::findOrFail($id);
        $tipoPrograma->delete();

        return response()->json([],204);
    }
}
