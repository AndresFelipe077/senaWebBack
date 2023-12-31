<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AsociacionCriteriosPlaneacion;
use App\Models\Planeacion;
use Illuminate\Http\Request;

class PlaneacionController extends Controller
{
    private $relations;

    public function __construct()
    {
        $this -> relations = [
            'actividadProyectos',
            'resultados',
            'criteriosEvaluacion',
            'actividadAprendizajes'
        ];
    }

    public function destroy(int $id)
    {
        try {
            $planeacion = Planeacion::findOrFail($id);
    
            // Primero eliminamos los registros asociados en asociacionCriteriosPlaneacion
            AsociacionCriteriosPlaneacion::where('id_planeacion', $id)->delete();
    
            $planeacion->delete();
    
            return response()->json(['message' => 'Registro eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el registro', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Verifica si la solicitud contiene JSON
            if ($request->isJson()) {
                $registros = $request->json()->all();
            } else {
                // Si no es JSON, asume que es form-data
                $registros = $request->all();
            }
    
            $ids = [];  
    
            if (array_key_exists(0, $registros)) {
            
                foreach ($registros as $registro) {
                    $planeacion = Planeacion::create($registro);
                    $ids[] = $planeacion->id;  
                }
            } else {
            
                $planeacion = Planeacion::create($registros);
                $ids[] = $planeacion->id;  
            }
    
            foreach ($ids as $id) {
                AsociacionCriteriosPlaneacion::create(['id_planeacion' => $id]);  
            }
    
            return response()->json(['message' => 'Registros guardados correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar los registros', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request,int $id){
 
        $data = $request -> all();

        $planeacion =  Planeacion::findOrFail($id);
        $planeacion -> fill($data);
        $planeacion -> save();
        $planeacion = Planeacion::with($this->relations)->findOrFail($planeacion->id);
        return response()->json($planeacion,203);

    }

    public function showByResultado(int $id)
    {
        $resultados = Planeacion::with($this->relations)
        ->where('idResultadoAprendizaje',$id);

        return response() -> json($resultados);
    }

    public function showByIdActividadProyecto(int $id)
    {
        $resultados = Planeacion::with($this->relations)
        ->where('idActividadProyecto',$id) -> get();

        return response() -> json($resultados);
    }

}
