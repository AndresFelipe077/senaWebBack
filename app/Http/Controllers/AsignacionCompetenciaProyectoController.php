<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\asignacionCompetenciaProyecto;
use Illuminate\Http\Request;

class AsignacionCompetenciaProyectoController extends Controller
{
    // public function store(Request $request){
    //     $data = $request -> all();
    //     $asignacionCompetenciaProyecto= asignacionCompetenciaProyecto::create($data);
    //     return response() -> json($asignacionCompetenciaProyecto);
    // }

    public function showByIdProyecto(int $id)
    {
        $fases = asignacionCompetenciaProyecto::with(['competencias','proyectosFormativos'])
        ->where('idProyecto',$id) -> get();

        return response() -> json($fases);
    }

    public function destroy(int $id)
    {
        $asignacionCompetenciaProyecto = asignacionCompetenciaProyecto::findOrFail($id);
        $asignacionCompetenciaProyecto ->delete();
    }

    public function store(Request $request)
    {
        // $registros = $request->input('asignacionCompetenciaProyecto');
        $registros = $request -> all();

        try {
            foreach ($registros as $registro) {
                asignacionCompetenciaProyecto::create($registro);
            }

            return response()->json(['message' => 'Registros guardados correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar los registros', 'error' => $e->getMessage()], 500);
        }
    }
}
