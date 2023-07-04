<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AsignacionParticipante;
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
                return $q->select('id')->where('id',$resultado)->orWhere('rap',$resultado);
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

        return response()->json($configuracionRap,203);
    }

    public function destroy($id)
    {
        $configuracionRap = configuracionRap::findOrFail($id);
        $configuracionRap->delete();

        return response()->json([]);
    }



    public function transferirFicha(Request $request)
{
    $participante_id = $request->input('participante_id');

    // Obtener los RAP aprobados y pendientes/cursando del participante
    $rap_aprobados = configuracionRap::where('idParticipante', $participante_id)
        ->where('idEstado', 'aprovado')
        ->pluck('idRap');

    $rap_pendientes_cursando = configuracionRap::where('idParticipante', $participante_id)
        ->whereIn('idEstado', ['pendiente', 'cursando'])
        ->pluck('idRap');

    // Verificar las validaciones
    if ($rap_aprobados->contains($request->input('resultado_destino'))) {
        return response()->json(['message' => 'Puede hacer el traslado de ficha']);
    } elseif ($rap_pendientes_cursando->contains($request->input('resultado_destino'))) {
        return response()->json(['message' => 'No puede hacer el traslado de ficha, aún no ha aprobado el RAP en el resultado destino']);
    } elseif ($rap_pendientes_cursando->count() > 0) {
        return response()->json(['message' => 'No puede hacer el traslado de ficha, debe terminar de cursar los RAP pendientes/cursando']);
    } else {
        return response()->json(['message' => 'No se puede realizar el traslado de ficha']);
    }
}

public function obtenerResultados($participante_id) {
    $participante = AsignacionParticipante::find($participante_id);

    $resultados = ConfiguracionRap::where('idParticipante', $participante->id)
        ->with('resultados')
        ->get();

    foreach ($resultados as $configuracion) {
        $resultadoParticipante = $configuracion->resultados;
        return response()->json(['holi']);
    }
}
}
