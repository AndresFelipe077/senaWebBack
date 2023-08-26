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
    public function crearHistorialDesdeRegistros()
    {
        try {
            DB::beginTransaction();
    
            $asignaciones = AsignacionParticipante::with(['estadoParticipantes:id,detalleEstado', 'tipoParticipacion:id,detalleParticipacion'])
                ->orderBy('id', 'asc')
                ->get(['id', 'observacion', 'fechaFinal', 'fechaInicial', 'idEstadoParticipantes', 'idTipoParticipacion']);
    
            $historialesPorId = [];
    
            foreach ($asignaciones as $asignacion) {
                // Obtener registros anteriores con el mismo idTipoParticipacion y ordenar por id
                $registrosAnteriores = AsignacionParticipante::where('idTipoParticipacion', $asignacion->idTipoParticipacion)
                    ->where('id', '<', $asignacion->id)
                    ->orderBy('id', 'asc')
                    ->get();
    
                // Actualizar registros anteriores y agregar historial actual
                foreach ($registrosAnteriores as $registroAnterior) {
                    $registroAnterior->fechaFinal = $asignacion->fechaInicial;
                    $registroAnterior->idEstadoParticipantes = 2; // Cambiar estado a "inactivo"
                    $historialAnterior = $historialesPorId[$registroAnterior->id][0];
                    $historialAnterior['fechaFinal'] = $registroAnterior->fechaFinal;
                    $historialAnterior['detalleEstado'] = 'INACTIVO';
                    $historialesPorId[$registroAnterior->id][0] = $historialAnterior;
    
                    $registroAnterior->save();
                }
    
                $historial = [
                    'observacion' => $asignacion->observacion,
                    'fechaFinal' => $asignacion->fechaFinal,
                    'fechaInicial' => $asignacion->fechaInicial,
                    'detalleEstado' => $asignacion->estadoParticipantes->detalleEstado,
                    'detalleParticipacion' => $asignacion->tipoParticipacion->detalleParticipacion,
                ];
    
                $historialesPorId[$asignacion->id][] = $historial;
            }
    
            DB::commit();
    
            return response()->json(['historialesPorId' => $historialesPorId, 'message' => 'Historiales de asignación creados con éxito'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Ha ocurrido un error'], 500);
        }
    }
    
}
