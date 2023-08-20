<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\HorarioInfraestructuraGrupo;

class HorarioInfraestructuraGrupoController extends Controller
{
    public function index()
    {
        $data = HorarioInfraestructuraGrupo::with(['infraestructura', 'grupo'])->get();
        return response()->json($data);
    }

    public function infraestructuraByGrupo(int $id)
    {
        $data = HorarioInfraestructuraGrupo::with(['infraestructura', 'grupo'])->where('idGrupo', $id)->get();
        return response()->json($data, 201);
    }

}
