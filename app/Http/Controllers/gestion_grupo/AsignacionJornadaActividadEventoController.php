<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Models\AsignacionJornadaActividadEvento;
use Illuminate\Http\Request;

class AsignacionJornadaActividadEventoController extends Controller
{


    public function index()
    {
        $data = AsignacionJornadaActividadEvento::with(['jornada', 'actividadEvento'])->get();
        return response()->json($data);
    }


    public function showByActividadEventos(int $id)
    {
        $data = AsignacionJornadaActividadEvento::with(['jornada', 'actividadEvento'])->where('idActividadEvento', $id)->get();
        return response()->json($data);
    }
}
