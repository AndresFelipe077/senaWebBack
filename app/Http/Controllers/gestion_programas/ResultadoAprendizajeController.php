<?php

namespace App\Http\Controllers\gestion_programas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\gestion_programas\CompetenciasController;
use App\Models\resultadoAprendizaje;
use App\Models\Competencias;
use Illuminate\Http\Request;

class resultadoAprendizajeController extends Controller{


    private $relations;

  public function __construct()
  {
    $this->relations = [
      'competencias',

    ];
  }
    public function index(Request $request)
    {
        $tipoResultado = $request->input('tipoRaps');
        $resultados = resultadoAprendizaje::with('tipoRaps');
        
// rrrrrrrevkizar


        if ($tipoResultado) {
            $resultados->whereHas('tipoRaps', function ($q) use ($tipoResultado) {
                return $q->where('id', $tipoResultado)->orWhere('nombre', $tipoResultado);
            });
        }

        return response()->json($resultados->get());
    }

    //funcion para asignar los resultados a las competencias
    public function store(Request $request)
    {




        $rap = resultadoAprendizaje::with($this -> relations)->get();

        //quitar pivots
        $newRap = $rap->map(function ($rapp) {
          $rapp['competecias'] = $rapp['competencias']->map(function ($infr) {
            $pivot = $infr['pivot'];
            unset($infr['pivot']);
            $infr['Competencias'] = $pivot;
            return $infr;
          });
    
          return $rapp;
          
        });
        return response()->json($newRap);
        // $data = $request->all();

        // if (isset($data['rap'])) {
        //     // Crear un nuevo resultado de aprendizaje
        //     $resultadoA = new resultadoAprendizaje($data);
        //     $resultadoA->save();
        //     // Verificar si se proporcionó un ID de competencia en la solicitud

        //     return response()->json($resultadoA, 201);
        // }

        // return response()->json(['error' => 'El campo "nombre" es requerido'], 400);
    }

    
    public function show(int $id)
    {
        $resultadoA = resultadoAprendizaje::find($id);

        return response()->json($resultadoA);
    }

    public function showByIdCompetencia(int $id)
    {
        $raps = resultadoAprendizaje::whereHas('competencias', function ($query) use ($id) {
            $query->where('idCompetencia', $id);
        })->get();

        return response() -> json($raps);
    }

    
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $resultadoA = resultadoAprendizaje::findOrFail($id);
        $resultadoA->fill($data);
        $resultadoA->save();

        return response()->json($resultadoA);
    }

    
    public function destroy(int $id)
    {
        $resultadoA = resultadoAprendizaje::findOrFail($id);
        $resultadoA->delete();
        return response()->json([
            'eliminado'
        ]);
    }

}
