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
        $proyectoFormativo = proyectoFormativo::find($id);
    
        if (!$proyectoFormativo) {
            return response()->json(['error' => 'Proyecto Formativo not found'], 404);
        }
    
        // Obtener las competencias asignadas al proyecto formativo
        $assignedCompetencias = $proyectoFormativo->asignacionCompetencias;
    
        // Obtener todas las competencias
        $allCompetencias = Competencias::all();
    
        // Filtrar las competencias no asignadas al proyecto formativo
        $unassignedCompetencias = $allCompetencias->diff($assignedCompetencias);
    
        return response()->json([
            'proyecto_formativo' => $proyectoFormativo,
            'competencias_no_asignadas' => $unassignedCompetencias->unique()
        ]);
        

    }

    public function eliminarCompetencias(Request $request, int $id)
{
    // Encuentra el proyecto formativo por su ID
    $proyectoFormativo = ProyectoFormativo::find($id);

    // Verifica si el proyecto formativo existe
    if (!$proyectoFormativo) {
        return response()->json(['error' => 'Proyecto Formativo not found'], 404);
    }

    // Obtiene el cuerpo de la solicitud como un array
    $requestData = $request->toArray();

    // Si se proporcionan competencias específicas, elimínalas
    if (isset($requestData['competencias'])) {
        $competencesToRemove = $requestData['competencias'];
        if (!is_array($competencesToRemove)) {
            $competencesToRemove = [$competencesToRemove];
        }
        $proyectoFormativo->asignacionCompetencias()->detach($competencesToRemove);

        return response()->json(['success' => 'Competencias eliminadas correctamente']);
    }
    // Si no se proporcionan competencias, no hagas nada
    return response()->json(['message' => 'No competencias provided to remove'], 400);
}

    
    public function assignCompetences(Request $request, int $id)
{
    // Encuentra el proyecto formativo por su ID
    $proyectoFormativo = ProyectoFormativo::find($id);

    // Verifica si el proyecto formativo existe
    if (!$proyectoFormativo) {
        return response()->json(['error' => 'Proyecto Formativo not found'], 404);
    }

    // Obtiene las IDs de las competencias a asignar
    $competencesToAssign = $request->input('competencias');

    // Si no se proporcionan competencias para asignar, devuelve un error
    if (!$competencesToAssign) {
        return response()->json(['error' => 'No competencias provided to assign'], 400);
    }

    // Si competencesToAssign no es un array, conviértelo en un array
    if (!is_array($competencesToAssign)) {
        $competencesToAssign = [$competencesToAssign];
    }

    // Asocia las competencias al proyecto formativo
    $proyectoFormativo->asignacionCompetencias()->attach($competencesToAssign);

    return response()->json(['success' => 'Competencias assigned successfully']);
}


}
