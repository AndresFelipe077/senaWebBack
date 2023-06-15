<?php

namespace App\Http\Controllers;

use App\Models\AsignacionParticipante;
use App\Models\Grupo;
use App\Models\Programa;
use Illuminate\Http\Request;

class AsignacionParticipanteController extends Controller
{
  public function index()
  {
    $data = AsignacionParticipante::with(['usuario','grupo']) -> get();
    return response() -> json($data);
  }



public function obtenerAsignacionesParticipantes()
{
    $asignaciones = AsignacionParticipante::with('grupo')->get();

    $data = [];
    foreach ($asignaciones as $asignacion) {
        $grupo = $asignacion->grupo;
        $idPrograma = $grupo->idPrograma;

        // Obtener todos los detalles del programa
        $programa = Programa::find($idPrograma);

        // Agregar los datos necesarios al arreglo
        $data[] = [
            'asignacionParticipantes' => $asignacion,


            'nombreGrupo' => $grupo->nombre,
            'nombrePrograma'=>$programa->nombrePrograma,




            'idPrograma' => $idPrograma,
            'programa' => $programa, // Agregar el programa completo
        ];
    }

    return response()->json($data);
}




public function obtenerGruposPorPrograma($idPrograma)
{
    $grupos = Grupo::where('idPrograma', $idPrograma)->get();

    return response()->json($grupos);
}

// public function obtenerAprendicesPorGrupo($idGrupo)
// {
//     $asignaciones = AsignacionParticipante::where('idGrupo', $idGrupo)
//         ->whereIn('idEstadoParticipantes', ['ACTIVO', 'PENDIENTE'])
//         ->with(['usuario'])
//         ->get();

//     return response()->json($asignaciones);
// }


public function obtenerAprendicesPorGrupo($idGrupo)
{
    $asignaciones = AsignacionParticipante::where('idGrupo', $idGrupo)
        ->whereIn('idEstadoParticipantes', function ($query) {
            $query->select('id')
                ->from('estadoParticipantes')
                ->whereIn('detalleEstado', ['ACTIVO', 'PENDIENTE']);
        })
        ->with(['usuario'])
        ->get();

    return response()->json($asignaciones);
}



}
