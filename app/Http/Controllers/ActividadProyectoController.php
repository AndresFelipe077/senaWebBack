<?php

namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use App\Models\ActividadProyecto;
use Illuminate\Http\Request;

class ActividadProyectoController extends Controller
{
    public function index(Request $request)
    {

        $fase = $request->input('fase');
        $AP = ActividadProyecto::with('fase');


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
        $actividadP = ActividadProyecto::with('fase')
        ->where('idFase',$id) -> get();

        return response() -> json($actividadP);
    }

    
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
