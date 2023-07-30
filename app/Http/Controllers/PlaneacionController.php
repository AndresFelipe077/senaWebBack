<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Planeacion;
use Illuminate\Http\Request;

class PlaneacionController extends Controller
{


    public function destroy(int $id)
    {
        $planeacion = Planeacion::findOrFail($id);
        $planeacion ->delete();
    }

    public function store(Request $request)
    {
        $registros = $request->all();

        try {
            foreach ($registros as $registro) {
        
                Planeacion::create($registro);
            }

            return response()->json(['message' => 'Registros guardados correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar los registros', 'error' => $e->getMessage()], 500);
        }
    }





}
