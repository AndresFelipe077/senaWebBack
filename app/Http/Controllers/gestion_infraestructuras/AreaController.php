<?php

namespace App\Http\Controllers\gestion_infraestructuras;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Area::with('infraestructuras') -> get();
        return response() -> json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $area = Area::create($request -> all());
        return $area;
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $area = Area::with('infraestructuras') -> find($id);
        return response() -> json($area);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        // Validar los datos recibidos del formulario
        $request->validate([
            'nombreArea' => 'required',
            'codigo' => 'required'
        ]);
    
        // Encontrar el registro a actualizar en la base de datos
        $registro = Area::findOrFail($id);
    
        // Actualizar los valores del registro
        $registro->nombreArea = $request->nombreArea;
        $registro->codigo = $request->codigo;
        $registro->iconUrl = $request->iconUrl;
        // Guardar los cambios en la base de datos
        $registro->save();
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
    }
}
