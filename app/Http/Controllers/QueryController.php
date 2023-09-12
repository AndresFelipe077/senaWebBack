<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Area;

class QueryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $table, string $query)
    {

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
    public function show(string $table_name,string $search)
    {
         // Verifica si la tabla especificada existe
         if (!Schema::hasTable($table_name)) {
            return response()->json(['error' => 'Tabla no encontrada'], 404);
        }

        // Realiza la búsqueda por hash en la tabla
        $resultados = $this->searchForHash($table_name, $search);

        if (empty($resultados)) {
            // Si no se encuentran resultados, puedes devolver una respuesta vacía o un mensaje adecuado
            return response()->json(['message' => 'No se encontraron resultados']);
        }

        // Si se encuentran resultados, devuelves el resultado deseado
        return response()->json(['resultados' => $resultados]);
    }

    public function searchForHash(string $table_name,string $search){

        $columnas = $this->getColumns($table_name);

        $registros = DB::table($table_name)->where(function ($query) use ($columnas, $search) {
            foreach ($columnas as $column) {
                $query->orWhere($column, 'LIKE', "%{$search}%");
            }
        })->get();

        if ($registros->isEmpty()) {
            return [];
        }

        // Convierte la colección de registros en un array asociativo utilizando una clave hash
        $resultados = [];

        foreach ($registros as $registro) {
            $claveHash = $registro->id; // Reemplaza 'id' con el campo adecuado para generar la clave hash

            if (!isset($resultados[$claveHash])) {
                $resultados[$claveHash] = [];
            }

            $resultados[$claveHash][] = $registro;
        }



        

        // Si solo hay un resultado, devuelves el objeto directamente
        if (count($resultados) === 1) {
            return reset($resultados);
        }

        // Si hay varios resultados, devuelves el array de objetos
        return $resultados;

    }

    public function getColumns(string $table_name){

        $columnas = Schema::getColumnListing($table_name);

        // Excluye columnas no deseadas, como columnas de tiempo
        $columnas = array_filter($columnas, function ($column) {
            return !in_array($column, ['created_at', 'updated_at','iconUrl']);
        });

        return $columnas;
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
