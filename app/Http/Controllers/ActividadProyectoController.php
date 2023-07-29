<?php

namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use App\Models\ActividadProyecto;
use Illuminate\Http\Request;

class ActividadProyectoController extends Controller
{
    public function index(Request $request)
    {

        $fase = $request->input('idFase');
        $AP = ActividadProyecto::with('faseProyecto');


        if($fase){
            $AP->whereHas('fase',function($q) use ($fase){
                return $q->select('id')->where('id',$fase)->orWhere('nombreFase',$fase);
            });
        };

        return response()->json($AP->get());
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $AP = new ActividadProyecto($data);
        $AP->save();

        return response()->json($AP,201);
    }


    public function show(int $id)
    {
        $AP = ActividadProyecto::find($id);

        return response()->json($AP,200);
    }

    public function showByIdFase(int $id){
        $actividadP = ActividadProyecto::with('faseProyecto.fase','faseProyecto.proyectoFormativo')
        ->where('idFaseProyecto',$id) -> get();

        return response() -> json($actividadP);
    }

//     $asignacionFaseProyFormativo = asignacionFaseProyFormativo::with('fase', 'proyectoFormativo')
//     ->where('idFaseProyecto', $id)
//     ->get();

// // Verifica si se encontró alguna asignación de fase con el ID dado
// if ($asignacionFaseProyFormativo->isEmpty()) {
//     return response()->json(['error' => 'Asignación de Fase no encontrada'], 404);
// }

// // Acceder a la fase relacionada desde la asignación de fase
// $faseRelacionada = $asignacionFaseProyFormativo[0]->fase;

// // Devolver la respuesta en formato JSON
// return response()->json([
//     'asignacion_fase_proyecto' => $asignacionFaseProyFormativo,
//     'fase_relacionada' => $faseRelacionada,
// ]);


    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $AP = ActividadProyecto::findOrFail($id);
        $AP->fill($data);
        $AP->save();

        return response()->json($AP,203);
    }

    public function destroy(int $id)
    {
        $AP = ActividadProyecto::findOrFail($id);
        $AP->delete();

        return response()->json(['eliminado con exito']);
    }
}
