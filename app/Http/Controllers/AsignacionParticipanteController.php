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

//   public function obtenerGruposPorPrograma(Request $request)
//   {
//       $programaId = $request->input('programa_id');

//       $grupos = Grupo::whereHas('asignacionParticipantes', function ($query) use ($programaId) {
//           $query->whereHas('grupo', function ($query) use ($programaId) {
//               $query->where('idPrograma', $programaId);
//           });
//       })->get();

//       return response()->json([
//           'grupos' => $grupos,
//       ]);
//   }



// public function obtenerGruposPorPrograma(Request $request)
// {
//     $programaId = $request->input('programa_id');

//     $grupos = Grupo::whereHas('asignacionParticipantes', function ($query) use ($programaId) {
//         $query->whereHas('grupo', function ($query) use ($programaId) {
//             $query->where('idPrograma', $programaId);
//         });
//     })
//     ->with('asignacionParticipantes')
//     ->get();

//     return response()->json([
//         'grupo' => $grupos,
//     ]);
// }




// public function obtenerAsignacionesParticipantes()
// {
//     $asignaciones = AsignacionParticipante::with('grupo')->get();

//     $data = [];
//     foreach ($asignaciones as $asignacion) {
//         $grupo = $asignacion->grupo;
//         $idPrograma = $grupo->idPrograma;

//         // Agregar los datos necesarios al arreglo
//         $data[] = [
//             'nombre' => $grupo->nombre,
//             'idPrograma' => $idPrograma,
//         ];
//     }

//     return response()->json([
//         'data' => $data,
//     ]);
// }




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

}
