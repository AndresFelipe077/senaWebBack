<?php

namespace App\Http\Controllers;

use App\Models\TipoCompetencias;
use App\Models\TipoRaps;
use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;

class TipoCompetenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $nombreTipoCompetencia = $request->input('nombre');

        $tipoRaps = TipoCompetencias::query();
        if($nombreTipoCompetencia){
            $tipoRaps->where('nombre',$nombreTipoCompetencia);
        }

        return response()->json($tipoRaps->get());

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $tipoCompetencia = TipoCompetencias::create($data);
        return response() -> json($tipoCompetencia);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipoCompetencias = TipoCompetencias::find($id);

        return response()->json($tipoCompetencias,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
