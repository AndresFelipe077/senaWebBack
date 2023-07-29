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
        $request->validate([
            'codigo' => 'required',
            'descripcion' => 'required'
        ]);
    
        $criterio = new ModelsCriteriosEvaluacion([
            'codigo' => $request->get('codigo'),
            'descripcion' => $request->get('descripcion')
        ]);
    
        $criterio->save();
    
        // Crear una nueva entrada en la tabla de asociación AsociacionCriteriosPlaneacion
        $asociacion = new AsociacionCriteriosPlaneacion();
        $asociacion->id_criterioEvaluacion = $criterio->id;  // Usar el ID del criterio de evaluación recién creado
        // No se requiere id_planeacion aquí
        $asociacion->save();
    
        return response()->json('Criterio de evaluación y su asociación guardados exitosamente!');
    }

    public function delete($id)
    {
        // Busca el registro del criterio de evaluación
        $criterio = ModelsCriteriosEvaluacion::find($id);
    
        // Si no se encuentra el criterio de evaluación, devuelve un error
        if (!$criterio) {
            return response()->json(['message' => 'Criterio de evaluación no encontrado'], 404);
        }
    
        // Elimina todas las asociaciones de este criterio en la tabla asociacionCriteriosPlaneacion
        AsociacionCriteriosPlaneacion::where('id_criterioEvaluacion', $id)->delete();
    
        // Elimina el criterio de evaluación
        $criterio->delete();
    
        // Retorna una respuesta
        return response()->json(['message' => 'Criterio de evaluación y todas las asociaciones relacionadas eliminadas con éxito'], 200);
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
