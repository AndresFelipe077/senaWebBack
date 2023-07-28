<?php
namespace App\Imports;


use App\Models\aprendicesTmp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AprendicesImport implements ToModel, WithHeadingRow

{
    public function model(array $row)
    {
        if (!empty($row['TIPO DOCUMENTO'])) {
            return new aprendicesTmp([
                'TIPO_DOCUMENTO' => $row['TIPO DOCUMENTO'],
                'IDENTIFICACION' => $row['IDENTIFICACION'],
                'NOMBRES' => $row['NOMBRES'],
                'APELLIDOS' => $row['APELLIDOS'],
                'ESTADO' => $row['ESTADO'],
                'FICHA' => $row['FICHA'],
                'PROGRAMA' => $row['PROGRAMA'],
                'PROYECTOFORMATIVO' => $row['PROYECTOFORMATIVO'],
            ]);
        }else{
             return redirect()->route('error')->with('message', 'El valor de TIPO_DOCUMENTO es nulo en la fila ' . $row['IDENTIFICACION']);
        }
    }
}
