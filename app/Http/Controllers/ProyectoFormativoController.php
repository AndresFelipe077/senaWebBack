<?php

namespace App\Http\Controllers;

use App\Models\proyectoFormativo;
use Illuminate\Http\Request;

class ProyectoFormativoController extends Controller
{   
    private $relations;

    public function __construct()
    {
        $this->relations = [
            'Programas',
            'fases',
            'centroFormativos'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Programa = $request->input('Programas');
        $centroFormacion = $request->input('CentroFormativos');
        $proyectoFormativo = proyectoFormativo::with($this ->relations)->get();

        if($Programa){
            $proyectoFormativo->whereHas('Programa',function($q) use ($Programa){
                return $q->select('id')->where('id',$Programa)->orWhere('nombrePrograma',$Programa);
            });
        };
        
        if($centroFormacion){
            $proyectoFormativo->whereHas('centroFormacion',function($q) use ($centroFormacion){
                return $q->select('id')->where('id',$centroFormacion)->orWhere('nombreCentro',$centroFormacion);
            });
        };

        //quitar pivots
        $newProyecto = $proyectoFormativo->map(function ($proyecto) {
            $proyecto['fases'] = $proyecto['fases']->map(function ($proyectoF) {
                $pivot = $proyectoF['pivot'];
                unset($proyectoF['pivot']);
                $proyectoF['fase_proyecto'] = $pivot;
                return $proyectoF;
            });
            return $proyecto;
        });

        return response()->json($newProyecto);
    }

    
    public function store(Request $request)
    {
        $data = $request->all();
        $proyectoFormativo = new proyectoFormativo($data);
        $proyectoFormativo->save();

        return response()->json($proyectoFormativo,201);
    }

    
    public function show(int $id)
    {
        $proyectoFormativo = proyectoFormativo::find($id);
        
        return response()->json($proyectoFormativo,200);
    }

    
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $proyectoFormativo = proyectoFormativo::findOrFail($id);
        $proyectoFormativo->fill($data);
        $proyectoFormativo->save();

        return response()->json($proyectoFormativo,203);
    }

    
    public function destroy(int $id)
    {
        $proyectoFormativo = proyectoFormativo::findOrFail($id);
        $proyectoFormativo->delete();

        return response()->json('eliminado con exito');
    }
}
