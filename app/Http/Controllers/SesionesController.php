<?php

namespace App\Http\Controllers;

use App\Models\Sesiones;
use Illuminate\Http\Request;

class SesionesController extends Controller
 {
        /**
         * Display a listing of the resource.
         */
        public function index(Request $request)
        {

            // $grupos = $request->input('configuracionRap');
            
            $Sesiones = Sesiones::with('configuracionRap', 'configuracionRap.grupos' ,'configuracionRap.usuarios')->get();

            if($Sesiones){
                $Sesiones->wherehas('configuracionRap',function($q) use ($Sesiones){
                    return $q->select('id')->where('id',$Sesiones)->orwhere('configuracionRap',$Sesiones);       
                });
            };

            return response()->json($Sesiones);
        }
        /**
         * Store a newly created resource in storage.
         */
        public function store(Request $request)
        {
        $data = $request->all();    
        $sesiones = new Sesiones($data);
        $sesiones ->save();
        return response()->json($sesiones,201);
            
        }

        public function show($id)
        {
            $sesion = Sesiones::with('configuracionRap')->find($id);
            return view('sesiones.show', compact('sesion'));
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
    
            $sesiones = Sesiones::findOrFail($id);
            $sesiones->update($data);
    
            return response()->json($sesiones, 200);
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
