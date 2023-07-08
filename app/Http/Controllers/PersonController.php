<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Person::with('usuario')->get();
        return response()->json($data);
    }


    public function personByIdentificacion($identificacion)
    {
        $personIdentificacion = Person::where('identificacion', $identificacion)->first();

        if ($personIdentificacion) {
            return response()->json(['message' => 'Se encontró la persona', 'person' => $personIdentificacion]);
        } else {
            return response()->json(['message' => 'No se encontró ninguna persona con esa identificación'], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $person)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */



     
     public function update(Request $request, $id)
     {
         // Valida los datos recibidos en la solicitud
         $validatedData = $request->validate([
             // Validación de campos...
         ]);
     
         // Encuentra la persona por su ID
         $persona = Person::find($id);
     
         // Actualiza el registro de la persona con los nuevos datos
         $persona->identificacion = $validatedData['identificacion'];
         $persona->nombre1 = $validatedData['nombre1'];
         $persona->nombre2 = $validatedData['nombre2'];
         $persona->apellido1 = $validatedData['apellido1'];
         $persona->apellido2 = $validatedData['apellido2'];
         $persona->fechaNac = $validatedData['fechaNac'];
         $persona->direccion = $validatedData['direccion'];
         $persona->email = $validatedData['email'];
         $persona->telefonoFijo = $validatedData['telefonoFijo'];
         $persona->celular = $validatedData['celular'];
         $persona->perfil = $validatedData['perfil'];
         $persona->sexo = $validatedData['sexo'];
         $persona->rh = $validatedData['rh'];
         $persona->rutaFoto = $validatedData['rutaFoto'];
         $persona->idTipoIdentificacion = $validatedData['idTipoIdentificacion'];
         $persona->idCiudad = $validatedData['idCiudad'];
         $persona->idCiudadNac = $validatedData['idCiudadNac'];
         $persona->idCiudadUbicacion = $validatedData['idCiudadUbicacion'];
     
         $persona->save();
     
         // Recupera la información de las ciudades usando las relaciones definidas en los modelos
         $ciudad = $persona->ciudad()->first();
         $ciudadNac = $persona->ciudadNac()->first();
         $ciudadUbicacion = $persona->ciudadUbicacion()->first();
     
         // Prepara los datos para la respuesta
         $responseData = $persona->toArray();
         $responseData['ciudad'] = $ciudad;
         $responseData['ciudadNac'] = $ciudadNac;
         $responseData['ciudadUbicacion'] = $ciudadUbicacion;
     
         return response()->json(['message' => 'Persona actualizada exitosamente', 'data' => $responseData], 200);
     }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        //
    }
}
