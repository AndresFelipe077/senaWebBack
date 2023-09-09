<?php

namespace App\Http\Controllers;

use App\Models\EstadoPrograma;
use Illuminate\Http\Request;

class EstadoProgramaController extends Controller
{
    public function index()
    {
        $estado = EstadoPrograma::all();
        return response() -> json($estado);
    }
}
