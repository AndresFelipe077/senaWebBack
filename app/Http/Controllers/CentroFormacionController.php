<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CentroFormacion;
use Illuminate\Http\Request;

class CentroFormacionController extends Controller
{
    
    public function index()
    {
        $data = CentroFormacion::with('regional')->get();

        return response()->json($data);
    }

    
    public function store(Request $request)
    {
        $data = $request->all();
        $AP = new CentroFormacion($data);
        $AP->save();

        return response()->json($AP,201);
    }

    
    public function show(int $id)
    {
        $AP = CentroFormacion::find($id);
        
        return response()->json($AP,200);
    }

    
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $AP = CentroFormacion::findOrFail($id);
        $AP->fill($data);
        $AP->save();

        return response()->json($AP,203);
    }

    
    public function destroy(int $id)
    {
        $AP = CentroFormacion::findOrFail($id);
        $AP->delete();

        return response()->json(['eliminado con exito']);
    }
}
