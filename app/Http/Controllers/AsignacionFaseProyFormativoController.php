<?php

namespace App\Http\Controllers;

use App\Models\asignacionFaseProyFormativo;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class AsignacionFaseProyFormativoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $asignacionFaseProyFormativo=asignacionFaseProyFormativo::create($data);

        return response()->json($asignacionFaseProyFormativo);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\asignacionFaseProyFormativo  $asignacionFaseProyFormativo
     * @return \Illuminate\Http\Response
     */
    public function show(asignacionFaseProyFormativo $asignacionFaseProyFormativo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\asignacionFaseProyFormativo  $asignacionFaseProyFormativo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, asignacionFaseProyFormativo $asignacionFaseProyFormativo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\asignacionFaseProyFormativo  $asignacionFaseProyFormativo
     * @return \Illuminate\Http\Response
     */
    public function destroy(asignacionFaseProyFormativo $asignacionFaseProyFormativo)
    {
        //
    }
}
