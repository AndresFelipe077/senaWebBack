<?php

namespace App\Http\Controllers\gestion_programas;

use App\Http\Controllers\Controller;
use App\Models\actividadAprendizaje;
use Illuminate\Http\Request;

class actividadAprendizajeController extends Controller
{   
    private $relations;

    public function __construct()
    {
        $this->relations = [
            'estado',
            'rap.resultados'
        ];
    }
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
        $actividadAprendizaje = actividadAprendizaje::with($this->relations);

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
        $actividadAA = actividadAprendizaje::with($this->relations)->findOrFail($actividadAA -> id);

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


    public function showByIdRap(int $id){
        $actividadesAprendizaje = actividadAprendizaje::with($this->relations)
        -> where('idPlaneacion',$id) -> get();

        return response() -> json($actividadesAprendizaje);
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
        $actividadAA = actividadAprendizaje::with($this->relations) -> findOrFail($actividadAA ->id);

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
