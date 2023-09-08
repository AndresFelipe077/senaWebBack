<?php

namespace App\Http\Controllers;

use App\Models\AsignacionFaseProyFormativo;
use Illuminate\Http\Request;

class AsignacionFaseProyFormativoController extends Controller
{   

    private $relations;

    public function __construct(){
        $this->relations = [
            'fase',
            'proyectoFormativo'
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $asignacionFaseProyFormativo=new asignacionFaseProyFormativo($data);
        $asignacionFaseProyFormativo -> save();
        $asignacionFaseProyFormativo = asignacionFaseProyFormativo::with($this->relations) ->findOrFail($asignacionFaseProyFormativo->id);

        return response()->json($asignacionFaseProyFormativo);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\asignacionFaseProyFormativo  $asignacionFaseProyFormativo
     * @return \Illuminate\Http\Response
     */
    public function show(asignacionFaseProyFormativo $asignacionFaseProyFormativo)
    {
        //
    }

    public function showByIdProyecto(int $id)
    {
        $fases = AsignacionFaseProyFormativo::with($this->relations)
        ->where('idProyectoFormativo',$id) -> get();

        return response() -> json($fases);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\asignacionFaseProyFormativo  $asignacionFaseProyFormativo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,int $id)
    {
        $data = $request -> all();
        $asignacion_fase = asignacionFaseProyFormativo::with($this->relations) 
        -> findOrFail($id);
        $asignacion_fase -> fill($data);
        $asignacion_fase -> save();

        return response()->json($asignacion_fase,204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\asignacionFaseProyFormativo  $asignacionFaseProyFormativo
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $asignacionFaseP = asignacionFaseProyFormativo::findOrFail($id);
        $asignacionFaseP -> delete();
    }
}
