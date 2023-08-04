<?php

namespace App\Http\Controllers\gestion_rol;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Permission\PermissionConst;
use Illuminate\Http\Request;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;


class RolController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:' . PermissionConst::GESTION_ROLES);
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        $nombre = $request->input('name');
        $company = $request->input('company');


        $roles = Rol::with("company");

        if ($nombre) {
            $roles->where('name', '=', $nombre);
        }

        if ($company) {
            $roles->whereHas('company', function ($q) use ($company) {
                $q->where('id', '=', $company);
            });
        }


        return response()->json($roles->get());


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


     public function store(Request $request)
     {
         $rol = new Rol();
         $rol->name = $request->input('name');
         $rol->idCompany = $request->input('idCompany');

         $imagen = $request->file('rutaFoto');

         if ($imagen) {
             $rutaImagen = $imagen->store('public/fotos');

             $urlImagen =   Storage::url($rutaImagen);

             $rol->rutaFoto = $urlImagen;
         }

         $rol->save();


         return response()->json(['message' => 'Rol creado correctamente', 'rol' => $rol], 201);
     }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $rol = Rol::find($id);

        return response()->json($rol);
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
        $rol = Rol::findOrFail($id);
        $rol->fill($data);
        $rol->save();

        return response()->json($rol);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $rol = Rol::findOrFail($id);
        $rol->delete();

        return response()->json([], 204);
    }


    public function verImagenRole($id)
    {
        $imagen = Rol::findOrFail($id);

        // Obtener la ruta completa del archivo
        $rutaArchivo = storage_path('app/public/' . $imagen->rutaFoto);

        // Verificar si el archivo existe
        if (!file_exists($rutaArchivo)) {
            return response()->json(['message' => 'El archivo no existe'], 404);
        }

        // Leer el contenido del archivo
        $contenido = file_get_contents($rutaArchivo);

        // Obtener el tipo de contenido de la imagen
        $tipoContenido = mime_content_type($rutaArchivo);

        // Generar la respuesta con el contenido del archivo y el tipo de contenido adecuado
        return Response::make($contenido, 200, [
            'Content-Type' => $tipoContenido,
            'Content-Disposition' => 'inline; filename="' . $imagen->nombre . '"'
        ]);
    }


    public function getUrlImagenRole($id)
    {
        $imagen = Rol::findOrFail($id);
        $rutaCompleta = 'http://localhost:8000/api/' . $imagen->rutaFoto; // Reemplaza localhost:8000 con la URL de tu servidor

        return response()->json(['urlImagen' => $rutaCompleta], 200);
    }

}
