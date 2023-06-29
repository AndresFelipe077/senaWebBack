<?php

namespace App\Http\Controllers;

use App\Models\TipoParticipacion;
use Illuminate\Http\Request;

class TipoParticipacionController extends Controller
{
    public function index()
    {
        $data = TipoParticipacion::with('tipoParticipacion')->get();
        return response()->json($data);
    }





}
