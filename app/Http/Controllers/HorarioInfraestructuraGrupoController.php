<?php

namespace App\Http\Controllers;

use App\Models\HorarioInfraestructuraGrupo;
use Database\Seeders\InfraestructuraSeeder;
use Illuminate\Http\Request;

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
