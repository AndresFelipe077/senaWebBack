<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

// use App\Models\aprendicesTmp;

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
    // public function importar()
    // {
    //     Excel::import(new AprendicesImport,  request()->file('documento')) ;
    //     return response()->json(['message'=>'ya no joda mas']);
        
    //     return response()->json(['error'=>'mensaje de error']);
    // }


    // public function  importar2(){
    //     Excel::import(new AprendicesImport, request()->file('doc'));
    //     return response()->json('hola mundo');
    // }
   



    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xls,xlsx'
    //     ]);

    //     $path = $request->file('file')->getRealPath();
    //     $data = Excel::import(new aprendicesImport, $path);

    //     return response()->json([
    //         'message' => 'Archivo importado exitosamente',
    //         'data' => $data,
    //     ]);
    // }





    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xls,xlsx'
    //     ]);
    //     $path = $request->file('file')->getRealPath();

    //     $spreadsheet = IOFactory::load($path);
    //     $worksheet = $spreadsheet->getActiveSheet();
    //     $data = $worksheet->toArray();

    //     foreach ($data as $row) {
    //         $aprendiz = new aprendicesTmp();
    //         $aprendiz->TIPO_DOCUMENTO = $row[0];
    //         $aprendiz->IDENTIFICACION = $row[1];
    //         $aprendiz->NOMBRES = $row[2];
    //         $aprendiz->APELLIDOS = $row[3];
    //         $aprendiz->ESTADO = $row[4];
    //         $aprendiz->FICHA = $row[5];
    //         $aprendiz->PROGRAMA = $row[6];
    //         $aprendiz->PROYECTOFORMATIVO = $row[7];
    //         $aprendiz->save();
    //     }

    //     return response()->json([
    //         'message' => 'Archivo importado exitosamente',
    //         'data' => $data,
    //     ]);
    // }











    // public function importExcel(Request $request)
    // {

    //     // Valida el archivo de Excel
    //     $request->validate([
    //         'excel_file' => 'required|mimes:xlsx,xls'
    //     ]);

    //     // Obtiene archivo
    //     $file = $request->file('excel_file');
      
    //     try {
    //         // Cargar el archivo utilizando PhpSpreadsheet
    //         $spreadsheet = IOFactory::load($file->getRealPath());
  
    //         // Obtener la primera hoja de cálculo
    //         $sheet = $spreadsheet->getActiveSheet();
            
    //         // Iterar por cada fila de datos (omitir la primera fila que contiene encabezados)
    //         foreach ($sheet->getRowIterator(2) as $row) {
    //             // Obtener los valores de cada celda
    //             $cellIterator = $row->getCellIterator();
    //             $cellIterator->setIterateOnlyExistingCells(false);

    //             $data = [];
    //             foreach ($cellIterator as $cell) {
    //                 $data[] = $cell->getValue();
    //             }
                
    //             // Verificar si los datos están completos
    //             if ($this->validateData($data)) {
    //                 // Crear un nuevo registro en la base de datos


    //                 $aprendiz = new aprendicesTmp([
    //                     'TIPO_DOCUMENTO' => $data[0],
    //                     'IDENTIFICACION' => $data[1],
    //                     'NOMBRES' => $data[2],
    //                     'APELLIDOS' => $data[3],
    //                     'ESTADO' => $data[4],
    //                     'FICHA' => $data[5],
    //                     'PROGRAMA' => $data[6],
    //                     'PROYECTOFORMATIVO' => $data[7]
    //                 ]);
                 
    //                 $aprendiz ->save();

    //                 var_dump($aprendiz);
    //             }
    //         }

    //         return response()->json(['message' => 'Datos importados correctamente si funciona'], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Error al importar el archivo'], 500);
    //     }
    // }

    // private function validateData($data)
    // {
    //     // Verificar si los datos están completos
    //     foreach ($data as $value) {
    //         if (empty($value)) {
    //             return false;
    //         }
            
    //     }

    //     return true;
    // }




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
