<?php

namespace App\Http\Controllers\gestion_programas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\gestion_programas\CompetenciasController;
use App\Models\resultadoAprendizaje;
use App\Models\Competencias;
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

    
    // public function store(Request $request)
    // { 

    //     $data = $request->all();
    //     $resultadoA = new resultadoAprendizaje($data);
    //     $resultadoA->save();

    //     return response()->json($resultadoA, 201);
    // }
    public function store(Request $request)
    {

        $data = $request->all();

        if (isset($data['rap'])) {
            // Crear un nuevo resultado de aprendizaje
            $resultadoA = new resultadoAprendizaje($data);
            $resultadoA->save();
            // Verificar si se proporcionó un ID de competencia en la solicitud
            if (isset($data['idCompetencia'])) {
                $competencia = Competencia::findOrFail($data['idCompetencia']);

                // Agregar la competencia al resultado de aprendizaje
                $resultadoA->competencias()->attach($competencia);
            }

            return response()->json($resultadoA, 201);
        }

        return response()->json(['error' => 'El campo "nombre" es requerido'], 400);
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

    public function agregarCompetencia($idResultadoAprendizaje, $idCompetencia)
    {
        $resultadoAprendizaje = resultadoAprendizaje::findOrFail($idResultadoAprendizaje);
        $competencia = Competencias::findOrFail($idCompetencia);

        // Agregar la competencia al resultado de aprendizaje
        $resultadoAprendizaje->competencias()->attach($competencia);

        return redirect()->back()->with('success', 'Competencia agregada correctamente al resultado de aprendizaje.');
    }

}
