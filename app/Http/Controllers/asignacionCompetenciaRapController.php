<?php

namespace App\Http\Controllers;

use App\Models\AsignacionCompetenciaRap;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class asignacionCompetenciaRapController extends Controller
{

    public function showByCompetencia(int $id)
    {
        $data = AsignacionCompetenciaRap::with(['competencias', 'resultadoAprendizaje'])
            ->where('idCompetencia', $id)
            ->get();
        return response()->json($data);
    }
}
