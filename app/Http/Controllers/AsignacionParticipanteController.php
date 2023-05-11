<?php

namespace App\Http\Controllers;

use App\Models\AsignacionParticipante;
use Illuminate\Http\Request;

class AsignacionParticipanteController extends Controller
{
  public function index()
  {
    $data = AsignacionParticipante::with(['usuario','grupo']) -> get();
    return response() -> json($data);
  }
  
}
