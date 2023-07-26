<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $planeacion = Planeacion::findOrFail($id);
        $planeacion ->delete();
    }
}
