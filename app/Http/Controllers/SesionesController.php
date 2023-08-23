<?php

namespace App\Http\Controllers;

use App\Models\Sesiones;
use Illuminate\Http\Request;

class SesionesController extends Controller
 {
        /**
         * Display a listing of the resource.
         */
        public function index()
        {
            $sesiones = Sesiones::with('configuracionRap.participantes')->get();
    
            return view('sesiones.index', compact('sesiones'));
        }
    
        /**
         * Display the specified resource.
         */
        public function show($id)
        {
            $sesion = Sesiones::with('configuracionRap')->find($id);
    
            return view('sesiones.show', compact('sesion'));
        }
    
        /**
         * Store a newly created resource in storage.
         */
        public function store(Request $request)
        {
            $data = $request->validate([
                'idConfiguracionRap' => 'required',
                'fecha' => 'required',
                'asistencia' => 'required',
                'horaLlegada' => 'required',
                'numberSesion' => 'required'
            ]);
    
            $sesion = Sesiones::create($data);
    
            return response()->json($sesion, 201);
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, $id)
        {
            $data = $request->validate([
                'idConfiguracionRap' => 'required',
                'fecha' => 'required',
                'asistencia' => 'required',
                'horaLlegada' => 'required',
                'numberSesion' => 'required'
            ]);
    
            $sesion = Sesiones::findOrFail($id);
            $sesion->update($data);
    
            return response()->json($sesion, 200);
        }
    
        /**
         * Remove the specified resource from storage.
         */
        public function destroy($id)
        {
            $sesion = Sesiones::findOrFail($id);
            $sesion->delete();
    
            return response()->json(null, 204);
        }
    }
