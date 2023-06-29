<?php

namespace App\Http\Controllers\gestion_programas;

use App\Http\Controllers\Controller;
use App\Models\actividadAprendizaje;
use Illuminate\Http\Request;

class actividadAprendizajeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        $estado = $request->input('estado');
        $ActividadAprendizaje = $request->input('rap');
        $actividadAprendizaje = actividadAprendizaje::with('estado', 'rap');

        if ($estado) {
            $actividadAprendizaje->whereHas('estado', function ($q) use ($estado) {
                return $q->select('id')->where('id', $estado)->orWhere('estado', $estado);
            });
        }

        if ($ActividadAprendizaje) {
            $actividadAprendizaje->whereHas('rap', function ($q) use ($ActividadAprendizaje) {
                return $q->select('id')->where('id', $ActividadAprendizaje)->orWhere('rap', $ActividadAprendizaje);
            });
        }

        return response()->json($actividadAprendizaje->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $actividadAA = new actividadAprendizaje($data);
        $actividadAA->save();

        return response()->json($actividadAA, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $actividadAA = actividadAprendizaje::find($id);

        return response()->json($actividadAA);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $actividadAA = actividadAprendizaje::findOrFail($id);
        $actividadAA->fill($data);
        $actividadAA->save();

        return response()->json($actividadAA);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $actividadAA = actividadAprendizaje::findOrFail($id);
        $actividadAA->delete();
        return response()->json(['eliminado'], 204);
    }
}
