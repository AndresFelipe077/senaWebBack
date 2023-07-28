<?php

namespace App\Http\Controllers;

use App\Models\AsignacionCompetenciaRap;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class asignacionCompetenciaRapController extends Controller
{
    public function store(Request $request){
        $data = $request -> all();
        $asignacionCompetenciaRap = asignacionCompetenciaRap::create($data);
        return response() -> json($asignacionCompetenciaRap);
    }

    public function showByCompetencia(int $id)
    {
        $data = AsignacionCompetenciaRap::with(['competencia', 'resultadoAprendizaje'])
            ->wh3ere('idCompetencia', $id)
            ->get();
        return response()->json($data);
    }

    public function destroy(int $id)
    {
        $asignacionCompetenciaRap = AsignacionCompetenciaRap::findOrFail($id);
        $asignacionCompetenciaRap ->delete();
    }
}
