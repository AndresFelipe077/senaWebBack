<?php

namespace App\Http\Controllers;

use App\Models\AsignacionFaseProyFormativo;
use Illuminate\Http\Request;

class AsignacionFaseProyFormativoController extends Controller
{

    private $relations;

    public function __construct()
    {
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
    public function index(Request $request)
    {
        $nombreFase = $request->input('descripcion');

        $fases = AsignacionFaseProyFormativo::query();
        if ($nombreFase) {
            $fases->where('descripcion', $nombreFase);
        }
        return response()->json($fases->get());
    }

    public function asignationExist(Request $request, ?int $id = null): bool
    {
        $data = $request->all();
        $idFase = $data['idFase'];
        $idProyecto = $data['idProyectoFormativo'];

        $asignacion_fase = null;

        if ($id) {
            $asignacion_fase = AsignacionFaseProyFormativo::find($id);
        }

        if ($asignacion_fase) {
            $status_fase = $asignacion_fase->idFase == $data['idFase'];
            $status_proyecto = $asignacion_fase->idProyectoFormativo == $data['idProyectoFormativo'];
            $found = !($status_fase && $status_proyecto);
            if(!$found){
                return $found;
            }
        }

        $found = AsignacionFaseProyFormativo::where('idFase', $idFase)
            ->where('idProyectoFormativo', $idProyecto)
            ->exists();

        return $found;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->asignationExist($request)) {
            $message = "Esta Fase ya fue asignada";
            return response()->json(['error' => $message], 400);
        }
        $data = $request->all();
        $asignacionFaseProyFormativo = new AsignacionFaseProyFormativo($data);
        $asignacionFaseProyFormativo->save();
        $asignacionFaseProyFormativo = AsignacionFaseProyFormativo::with($this->relations)->findOrFail($asignacionFaseProyFormativo->id);

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
            ->where('idProyectoFormativo', $id)->get();

        return response()->json($fases);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\asignacionFaseProyFormativo  $asignacionFaseProyFormativo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        if ($this->asignationExist($request,$id)) {
            $message = "Esta fase ya fue asignada a este proyecto ";
            return response()->json(['error' => $message], 400);
        }
        $data = $request->all();
        $asignacion_fase = AsignacionFaseProyFormativo::findOrFail($id);
        $asignacion_fase->fill($data);
        $asignacion_fase->save();
        $asignacion_fase = AsignacionFaseProyFormativo::with($this->relations)->findOrFail($asignacion_fase->id);


        return response()->json($asignacion_fase);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\asignacionFaseProyFormativo  $asignacionFaseProyFormativo
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $asignacionFaseP = AsignacionFaseProyFormativo::findOrFail($id);
        $asignacionFaseP->delete();
    }
}
