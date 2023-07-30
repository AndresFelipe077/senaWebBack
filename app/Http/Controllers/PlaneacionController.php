<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AsociacionCriteriosPlaneacion;
use App\Models\Planeacion;
use Illuminate\Http\Request;

class PlaneacionController extends Controller
{
    public function store(Request $request){
        $data = $request -> all();
        $planeacion = Planeacion::create($data);
        return response() -> json($planeacion);
    }

    public function showByIdActividadProyecto(int $id)
    {
        $resultados = Planeacion::with(['actividadProyectos','resultados'])
        ->where('idActividadProyecto',$id) -> get();

        return response() -> json($resultados);
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
}
