<?php

namespace App\Http\Controllers;

use App\Models\AsignacionParticipante;
use App\Models\estadoParticipante;
use App\Models\Proceso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistorialDocumentoController extends Controller
{
    public function index(Request $request)
    {
        $nombreProceso = $request->input('nombreProceso');

        $procesos = Proceso::query();
        if ($nombreProceso) {
            $procesos->where('nombreProceso', $nombreProceso);
        }

        return response()->json($procesos->get());
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
