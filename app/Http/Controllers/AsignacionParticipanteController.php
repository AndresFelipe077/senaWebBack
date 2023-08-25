<?php

namespace App\Http\Controllers;

use App\Models\AsignacionParticipante;
use App\Models\estadoParticipante;
use App\Models\Grupo;
use App\Models\Programa;
use App\Models\proyectoFormativo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionParticipanteController extends Controller

{



    private $relations;

    public function __construct()
    {
        $this->relations = [
            'grupo',
            'usuario',
            'tipoParticipacion',
            'EstadoParticipante',
        ];
    }
    public function index()
    {
        $data = AsignacionParticipante::with(['usuario', 'grupo'])->get();
        return response()->json($data);
    }



   public function obtenerAsignacionesParticipantes()
{
    $asignaciones = AsignacionParticipante::with('grupo')->get();

    $data = [];
    foreach ($asignaciones as $asignacion) {
        $grupo = $asignacion->grupo;

        // Verificar si el grupo es válido antes de acceder a la propiedad idPrograma
        if ($grupo) {
            $idPrograma = $grupo->idPrograma;

            // Obtener todos los detalles del programa
            $programa = Programa::find($idPrograma);

            // Agregar los datos necesarios al arreglo
            $data[] = [
                'asignacionParticipantes' => $asignacion,
                'nombreGrupo' => $grupo->nombre,
                'nombrePrograma' => $programa ? $programa->nombrePrograma : null, // Verificar si $programa es válido
                'idPrograma' => $idPrograma,
                'programa' => $programa, // Agregar el programa completo
            ];
        }
    }

    return response()->json($data);
}



public function crearHistorialDesdeRegistros()
{
   
    $data = AsignacionParticipante::with(['usuario', 'grupo'])->get();
    return response()->json($data);
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

        // $usuariosActivados = ActivationCompanyUser::role('APRENDIZ')->active()->get();

        $asignaciones = AsignacionParticipante::where('idGrupo', $idGrupo)
            ->whereIn('idEstadoParticipantes', function ($query) {
                $query->select('id')
                    ->from('estadoParticipantes')
                    ->whereIn('detalleEstado', ['ACTIVO', 'PENDIENTE']);
            })
            ->with(['usuario.persona'])
            ->get();

        return response()->json($asignaciones);
    }


}
