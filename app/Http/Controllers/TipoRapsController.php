<?php

namespace App\Http\Controllers;

use App\Models\TipoRaps;
use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;

class TipoRapsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $nombreTipoRap = $request->input('nombre');

        $tipoRaps = TipoRaps::query();
        if($nombreTipoRap){
            $tipoRaps->where('nombre',$nombreTipoRap);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipoRaps = TipoRaps::find($id);

        return response()->json($tipoRaps,200);
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
