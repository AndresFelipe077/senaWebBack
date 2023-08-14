<?php

namespace App\Http\Controllers;

use App\Models\Fase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class FaseController extends Controller
{

    public function index(Request $request)
    {
        $nombreFase = $request->input('nombreFase');

        $fases = Fase::query();
        if ($nombreFase) {
            $fases->where('nombreFase', $nombreFase);
        }
        return response()->json($fases->get());
    }



    public function store(Request $request)
    {
        $data = $request->all();
        $fase = new Fase($data);
        $fase->save();

        return response()->json($fase, 201);
    }


    public function show(int $id)
    {
        $fase = Fase::find($id);

        return response()->json($fase, 200);
    }

    public function update(Request $request, int  $id)
    {
        $data = $request->all();
        $fase = Fase::findOrFail($id);
        $fase->fill($data);
        $fase->save();

        return response()->json($fase);
    }


    public function destroy(int $id)
    {
        $fase = Fase::findOrFail($id);
        $fase->delete();

        return response()->json(['eliminado con exito']);
    }
}
