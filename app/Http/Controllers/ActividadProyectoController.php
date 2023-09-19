<?php

namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use App\Models\ActividadEvento;
use App\Models\ActividadProyecto;
use Illuminate\Http\Request;

class ActividadProyectoController extends Controller
{

    private $relations;

    public function __construct()
    {
        $this->relations = [
            'faseProyecto.fase',
            'faseProyecto.proyectoFormativo'
        ];
    }

    public function index(Request $request)
    {
        $fase = $request->input('faseProyecto');
        $AP = ActividadProyecto::with($this->relations);

        if ($fase) {
            $AP->whereHas('faseProyecto', function ($q) use ($fase) {
                return $q->select('id')->where('id', $fase)->orWhere('nombreFase', $fase);
            });
        };

        return response()->json($AP->get());
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $AP = new ActividadProyecto($data);
        $AP->save();
        $AP = ActividadProyecto::with($this->relations)->findOrFail($AP->id);

        return response()->json($AP, 201);
    }


    public function show(int $id)
    {
        $AP = ActividadProyecto::find($id);

        return response()->json($AP, 200);
    }

    public function showByIdFase(int $id)
    {
        $actividadP = ActividadProyecto::with($this->relations)
            ->where('idFaseProyecto', $id)->get();

        return response()->json($actividadP);
    }

    public function shoyByIdProyecto(int $id)
    {
        $actividadesP = ActividadProyecto::with($this->relations)
        ->whereHas('faseProyecto.proyectoFormativo',function ($query) use ($id){
            $query -> where('id',$id);
        }) -> get();

        return response() -> json($actividadesP);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $AP = ActividadProyecto::findOrFail($id);
        $AP->fill($data);
        $AP->save();
        $AP = ActividadProyecto::with($this->relations)->findOrFail($AP->id);

        return response()->json($AP, 203);
    }

    public function destroy(int $id)
    {
        $AP = ActividadProyecto::findOrFail($id);
        $AP->delete();

        return response()->json(['eliminado con exito']);
    }
}
