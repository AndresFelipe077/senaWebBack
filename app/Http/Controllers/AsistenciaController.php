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
            
            $Asistencia = Asistencia::with('configuracionRap', 'configuracionRap.grupos' ,'configuracionRap.usuarios')->get();

            if($Asistencia){
                $Asistencia->wherehas('configuracionRap',function($q) use ($Asistencia){
                    return $q->select('id')->where('id',$Asistencia)->orwhere('configuracionRap',$Asistencia);       
                });
            };

            return response()->json($Asistencia);
        }
        /**
         * Store a newly created resource in storage.
         */
        public function store(Request $request)
        {
        $data = $request->all();    
        $asistencia = new Asistencia($data);
        $asistencia ->save();
        return response()->json($asistencia,201);
            
        }

        public function show($id)
        {
            $asistencia = Asistencia::with('configuracionRap')->find($id);
            return response()->json($asistencia);
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, $id)
        {
            $data = $request->validate([
                'idConfiguracionRap' => 'required',
                'idAsignacionParticipnte'=>'required',
                'fecha' => 'required',
                'asistencia' => 'required',
                'horaLlegada' => 'required',
                'numberSesion' => 'required'
            ]);
    
            $asistencia = Asistencia::findOrFail($id);
            $asistencia->update($data);
    
            return response()->json($sesiones, 200);
        }
    
        /**
         * Remove the specified resource from storage.
         */
        public function destroy($id)
        {
            $asistencia = Asistencia::findOrFail($id);
            $asistencia->delete();
    
            return response()->json(null, 204);
        }
    }
