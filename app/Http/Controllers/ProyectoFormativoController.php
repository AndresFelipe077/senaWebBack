<?php

namespace App\Http\Controllers;

use App\Models\asignacionCompetenciaProyecto;
use App\Models\Competencias;
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

            $Proyecto = asignacionCompetenciaProyecto::with('competencias', 'proyectosFormativos')
            // ->where('company_id', $id)
            ->get();

        return response()->json($Proyecto);

            // return $proyecto;
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

    public function showByIdPrograma(int $id){
        $proyectos = proyectoFormativo::with($this -> relations) 
        -> where('idPrograma',$id) -> get();
        return response() -> json($proyectos);
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

    public function filtrarCompetenciasAsignadas($id)
    {
        $asignacionCompetenciaProyecto = asignacionCompetenciaProyecto::with('competencias')->find($id);
    
        
        if (!$asignacionCompetenciaProyecto) {
            return response()->json(['error' => 'Competencia not found'], 404);
        }
    
        // se utiliza funcion para traer los roles que estan en activationCompanyUser
        $assignedCompetencias = $asignacionCompetenciaProyecto->competencias;
    
        
        $allCompetencias = Competencias::all();
    
        // se crea filtro para obtener los filtros que no estan en activationCompanyUser
        $unassignedCompetencias = $allCompetencias->diff($assignedCompetencias);
    
       
        return response()->json([
            'assigned_competencias' => $assignedCompetencias,
            'unassigned_Competencias' => $unassignedCompetencias
        ]);
    }


}
