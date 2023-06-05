<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Http\Requests\StorematriculaRequest;
use Illuminate\Http\Request;

class MatriculaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $matricula = Matricula::with('ProyectoFormativo', 'grupo')->get();

        return response()->json($matricula);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorematriculaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $matricula = new Matricula($data);
        $matricula->save();

        return response()->json($matricula, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $tipoDocumento = Matricula::find($id);

        return response()->json($tipoDocumento);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $tipoDocumento = Matricula::findOrFail($id);
        $tipoDocumento->fill($data);
        $tipoDocumento->save();

        return response()->json($tipoDocumento);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $tipoDocumento = Matricula::findOrFail($id);
        $tipoDocumento->delete();

        return response()->json([], 204);
    }
}
