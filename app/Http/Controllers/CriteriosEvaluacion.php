<?php

namespace App\Http\Controllers;

use App\Models\AsociacionCriteriosPlaneacion;
use App\Models\criteriosEvaluacion as ModelsCriteriosEvaluacion;
use App\Models\Planeacion;
use Illuminate\Http\Request;

class CriteriosEvaluacion extends Controller
{

    
    public function index()
    {
        $criterios = ModelsCriteriosEvaluacion::all();
        return response() -> json($criterios);
    }


    public function store(Request $request)
    {
        // Buscar el primer registro existente en la tabla de asociación AsociacionCriteriosPlaneacion
        // donde el campo id_criterioEvaluacion es null
        $asociacion = AsociacionCriteriosPlaneacion::whereNull('id_criterioEvaluacion')->first();
    
        // Si no hay registros con id_criterioEvaluacion en null, retornar un mensaje de error
        if (!$asociacion) {
            return response()->json('No se puede crear un nuevo criterio de evaluación porque no hay asociaciones disponibles para actualizar', 400);
        }
    
        $request->validate([
            'codigo' => 'required',
            'descripcion' => 'required'
        ]);
    
        $criterio = new ModelsCriteriosEvaluacion([
            'codigo' => $request->get('codigo'),
            'descripcion' => $request->get('descripcion')
        ]);
    
        $criterio->save();
    
        // Actualizar el campo id_criterioEvaluacion del registro encontrado
        $asociacion->id_criterioEvaluacion = $criterio->id;  // Usa el ID del criterio de evaluación recién creado
        $asociacion->save();
    
        return response()->json('Criterio de evaluación guardado y asociación actualizada exitosamente!');
    }

    public function delete($id)
{
    // Busca el registro del criterio de evaluación
    $criterio = ModelsCriteriosEvaluacion::find($id);

    // Si no se encuentra el criterio de evaluación, devuelve un error
    if (!$criterio) {
        return response()->json(['message' => 'Criterio de evaluación no encontrado'], 404);
    }

    // Establece id_criterioEvaluacion a null en todas las asociaciones de este criterio en la tabla asociacionCriteriosPlaneacion
    AsociacionCriteriosPlaneacion::where('id_criterioEvaluacion', $id)->update(['id_criterioEvaluacion' => null]);

    // Elimina el criterio de evaluación
    $criterio->delete();

    // Retorna una respuesta
    return response()->json(['message' => 'Criterio de evaluación eliminado y todas las asociaciones relacionadas actualizadas con éxito'], 200);
}

    public function update(Request $request, $id)
    {
        // Valida la solicitud
        $request->validate([
            'codigo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
        ]);
    
        // Busca el criterio de evaluación
        $criterio = ModelsCriteriosEvaluacion::find($id);
    
        // Si no se encuentra el criterio de evaluación, devuelve un error
        if (!$criterio) {
            return response()->json(['message' => 'Criterio de evaluación no encontrado'], 404);
        }
    
        // Actualiza el código y la descripción del criterio de evaluación
        $criterio->codigo = $request->input('codigo');
        $criterio->descripcion = $request->input('descripcion');
        $criterio->save();
    
        // Retorna una respuesta
        return response()->json(['message' => 'Criterio de evaluación actualizado con éxito'], 200);
    }



}
