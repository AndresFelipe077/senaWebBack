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
            ->where('idCompetencia', $id)
            ->get();
        return response()->json($data);
    }
}
