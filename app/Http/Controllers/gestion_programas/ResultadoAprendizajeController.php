<?php

namespace App\Http\Controllers\gestion_programas;

use App\Http\Controllers\Controller;
use App\Models\resultadoAprendizaje;
use Illuminate\Http\Request;

class resultadoAprendizajeController extends Controller{

    private $relations;


    
    
    public function __construct()
    {
        $this->relations = [
            'competencia',
        ];
    }


    public function index(Request $request)
    {
        $competencia = $request->input('competencias');
        $resultados = resultadoAprendizaje::with('competencia');
        
        if ($competencia) {
            $resultados->whereHas('competencia', function ($q) use ($competencia) {
                return $q->where('id', $competencia)->orWhere('nombreCompetencia', $competencia);
            });
        }

        return response()->json($resultados->get());
    }

    //funcion para asignar los resultados a las competencias
    public function store(Request $request)
    {
        $data =$request->all();
        $resultado = new resultadoAprendizaje($data);
        $resultado->save();
  
        return response()->json($resultado);
    }

    
    public function show(int $id)
    {
        $resultadoA = resultadoAprendizaje::find($id);

        return response()->json($resultadoA);
    }

    public function showByIdCompetencia(int $id)
    {
            $resultados = resultadoAprendizaje::with($this -> relations ) 
            -> where('idCompetencia',$id) -> get();
            return response() -> json($resultados);
    }

    public function showByIdActividaP(int $id)
    {
            $resultados = resultadoAprendizaje::with($this -> relations ) 
            -> where('idCompetencia',$id) -> get();
            return response() -> json($resultados);
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
