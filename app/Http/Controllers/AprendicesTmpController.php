<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;



use App\Imports\AprendicesImport;
use App\Models\aprendicesTmp;
use Maatwebsite\Excel\Facades\Excel;





class AprendicesTmpController extends Controller
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



public function prueba(Request $request){
        // Validar si se envió un archivo
        if (!$request->hasFile('excelFile')) {
            return response()->json(['error' => 'No se envió ningún archivo'], 400);
        }
        // Obtener el archivo
        $file = $request->file('excelFile');
        // Validar el tipo de archivo
        if ($file->getClientOriginalExtension() !== 'xlsx') {
            return response()->json(['error' => ' Extension incompatible El archivo debe ser de tipo XLSX'], 400);
        }
        // Crear una instancia del lector de archivos de Excel
        $reader = IOFactory::createReader('Xlsx');
        // Cargar el archivo en un objeto Spreadsheet
        $documento = $reader->load($file->getPathname());
        // Obtener la primera hoja del documento
        $hojaActual = $documento->getSheet(0);
        // Obtener el rango de celdas no vacías
        $cellRange = $hojaActual->calculateWorksheetDimension();
        // Iterar por cada fila (empezando desde la segunda fila)
        foreach ($hojaActual->getRowIterator(2) as $fila) {
            $datosFila = [];
            // Iterar por cada celda en la fila actual
            foreach ($fila->getCellIterator() as $celda) {
                $datosFila[] = $celda->getValue();
            }
            // Crear una instancia de CargaNomina y asignar los valores de las celdas
            $cargaNomina = new aprendicesTmp;
            $cargaNomina->TIPO_DOCUMENTO = $datosFila[0];
            $cargaNomina->IDENTIFICACION = $datosFila[1];
            $cargaNomina->NOMBRES = $datosFila[2];
            $cargaNomina->APELLIDOS = $datosFila[3];
            $cargaNomina->ESTADO = $datosFila[4];
            $cargaNomina->FICHA = $datosFila[5];
            $cargaNomina->PROGRAMA = $datosFila[6];
            $cargaNomina->PROYECTOFORMATIVO= $datosFila[7];
     
            $cargaNomina->save();
        }
        return response()->json(['message' => 'Archivo importado correctamente']);
        }
}
