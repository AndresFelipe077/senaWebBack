<?php

namespace App\Http\Controllers\gestion_infraestructuras;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    /**
     * Display a listing of the resource.
    */
    public function index()
    {
        $data = Sede::with([
            'ciudad',
            'centroFormacion',
            'infraestructuras'
        ]) -> get();
        return response() -> json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $test = json_decode($request->getContent(),false);
        if(is_array($test)){
            $data = $request -> all();
            foreach ($data as $item) {
                $sede = new Sede();
                $sede = $this -> guardarSede($item);
                $sede -> save();
            }
            return response() -> json($data);
        }
        if(is_object($test)){
            $data = $request -> all();
            $sede = new Sede();
            $sede = $this -> guardarSede($data);
            $sede -> save();
            return response() -> json($data);
        }

    }
    private function guardarSede(Array $data){
        $sede = new Sede([
            'nombreSede' => $data['nombreSede'],
            'direccion' => $data['direccion'],
            'telefono' => $data['telefono'],
            'descripcion' => $data['descripcion'],
            'idCiudad' => $data['idCiudad'],
            'idCentroFormacion' => $data['idCentroFormacion']
        ]);
        return $sede;
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $sede = Sede::with(['ciudad','centroFormacion','infraestructuras']) -> find($id);
        return response() -> json($sede);
    }

    /**
     * Muestra las sedes dependiendo de la ciudad
     */
    public function showByCiudad(int $id){
        $sedes = Sede::with(['ciudad','centroFormacion','infraestructuras'])
            -> where('idCiudad',$id)
            -> get();

        return response() -> json($sedes);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,int $id)
    {
        // Validar los datos recibidos del formulario
        $request->validate([
            'nombreSede' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'idCiudad' => 'required'
        ]);

        // Encontrar el registro a actualizar en la base de datos
        $registro = Sede::findOrFail($id);

        // Actualizar los valores del registro
        $registro->nombreSede = $request->nombreSede;
        $registro->direccion = $request->direccion;
        $registro->telefono = $request->telefono;
        $registro->descripcion = $request->descripcion;
        $registro->idCiudad = $request->idCiudad;
        $registro->idCentroFormacion = $request -> idCentroFormacion;

        // Guardar los cambios en la base de datos
        $registro->save();

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $sede = Sede::findOrFail($id);
        $sede->delete();

    }
}
