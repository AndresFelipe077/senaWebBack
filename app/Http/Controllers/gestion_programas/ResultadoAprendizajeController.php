<?php

namespace App\Http\Controllers\gestion_programas;

use App\Http\Controllers\Controller;
use App\Models\resultadoAprendizaje;
use Illuminate\Http\Request;

class resultadoAprendizajeController extends Controller{
    public function index(Request $request)
    {
        $competencia = $request->input('competencias');
        $tipoResultado = $request->input('tipoRaps');
        $resultados = resultadoAprendizaje::with('competencias','tipoRaps');

        if($competencia){
            $resultados->whereHas('competencias',function($q) use ($competencia){
                return $q->select('id')->where('id', $competencia)->orWhere('nombreCompetencia',$competencia);
            });
        };

        if($tipoResultado){
            $resultados->whereHas('tipoRaps',function($q) use ($tipoResultado){
                return $q->select('id')->where('id', $tipoResultado)->orWhere('nombre',$tipoResultado);
            });
        }

        return response()->json($resultados->get());
    }

    
    public function store(Request $request)
    {
        $data = $request->all();
        $resultadoA = new resultadoAprendizaje($data);
        $resultadoA->save();

        return response()->json($resultadoA, 201);
    }

    
    public function show(int $id)
    {
        $resultadoA = resultadoAprendizaje::find($id);

        return response()->json($resultadoA);
    }

    
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $resultadoA = resultadoAprendizaje::findOrFail($id);
        $resultadoA->fill($data);
        $resultadoA->save();

        return response()->json($resultadoA);
    }

    
    public function destroy(int $id)
    {
        $resultadoA = resultadoAprendizaje::findOrFail($id);
        $resultadoA->delete();
        return response()->json([
            'eliminado'
        ]);
    }
}
