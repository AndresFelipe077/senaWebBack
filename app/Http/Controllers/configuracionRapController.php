<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\configuracionRap;

class configuracionRapController extends Controller
{

    public function index(Request $request)
    {
        $resultado = $request->input('resultados');
        $participante = $request->input('participantes');
        $estado = $request->input('estados');
        $jornada = $request->input('jornadas');
        $configuraconRap = configuracionRap::with('resultados','participantes','estados','jornadas');

        if($resultado){
            $configuraconRap->whereHas('resultados',function($q) use ($resultado){
                return $q->select('id')->where('id',$resulado)->orWhere('rap',$resultado);
            });
        };

        if($participante){
            $configuraconRap->whereHas('participantes',function($q) use ($participante){
                return $q->select('id')->where('id',$participante)->orWhere('participantes',$participante);
            });
        };

        if($estado){
            $configuraconRap->whereHas('estados',function($q) use ($estado){
                return $q->select('id')->where('id',$estado)->orWhere('estados',$estado);
            });
        };

        if($jornada){
            $configuraconRap->whereHas('jornadas',function($q) use ($jornada){
                return $q->select('id')->where('id',$jornada)->orWhere('jornadas',$jornada);
            });
        };


        return response()->json($configuraconRap->get());
    }


    public function store(Request $request)
    {
        $data = $request->all();

        $configuracionRap = new configuracionRap($data);
        $configuracionRap->save();
        return response()->json($configuracionRap,201);
    }


    public function show($id)
    {
        $configuracionRap = configuracionRap::find($id);
        return response()->json($configuracionRap,200);
    }


    public function update(Request $request, $id)
    {
        $data = $request->all();
        $configuracionRap = configuracionRap::findOrFail($id);
        $configuracionRap->fill($data);
        $configuracionRap->save();

        return response()->json($programa,203);
    }

    public function destroy($id)
    {
        $configuracionRap = configuracionRap::findOrFail($id);
        $configuracionRap->delete();

        return response()->json([]);
    }

    public function transferirFicha(Request $request)
{
    $fichaActual = $request->input('ficha_actual');
    $fichaDestino = $request->input('ficha_destino');
    $participante = $request->input('participante_id');
    
    // Obtener información del participante en la ficha actual
    $configuracionActual = ConfiguracionRap::where('idParticipante', $participante)
        ->where('idJornada', $fichaActual)
        ->first();
    
    // Obtener información del participante en la ficha destino
    $configuracionDestino = ConfiguracionRap::where('idParticipante', $participante)
        ->where('idJornada', $fichaDestino)
        ->first();
    
    // Verificar las condiciones para el traslado de ficha
    if ($configuracionActual && $configuracionDestino) {
        $estadoActual = $configuracionActual->estado->nombre; // Obtener el nombre del estado actual
        $estadoDestino = $configuracionDestino->estado->nombre; // Obtener el nombre del estado en la ficha destino
        
        if ($estadoActual == 'aprobado' && $estadoDestino == 'pendiente') {
            // Caso 1: El participante ya aprobó el rap en la ficha actual y la ficha destino aún no lo tiene aprobado
            // Realizar el traslado de ficha aquí
            // ...
            return response()->json(['message' => 'Traslado de ficha exitoso.']);
        } elseif ($estadoActual == 'pendiente' && $estadoDestino == 'aprobado') {
            // Caso 2: El participante aún no aprueba el rap y la ficha destino ya lo tiene aprobado
            return response()->json(['message' => 'No puedes realizar el traslado de ficha en este caso.']);
        } elseif ($estadoActual == 'cursando' && $estadoDestino == 'pendiente') {
            // Caso 3: El participante está cursando el rap en la ficha actual y la ficha destino aún no lo tiene aprobado
            return response()->json(['message' => 'El participante debe aprobar el rap antes de hacer el traslado de ficha.']);
        } else {
            // Otros casos no especificados
            return response()->json(['message' => 'No se cumple ninguna condición para el traslado de ficha.']);
        }
    } else {
        // No se encontraron las configuraciones de la ficha actual o destino
        return response()->json(['message' => 'No se encontró la configuración de la ficha actual o destino.']);
    }
}

}
