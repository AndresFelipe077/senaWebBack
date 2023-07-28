<?php

namespace App\Http\Controllers;

use App\Imports\AprendicesImport;
use App\Imports\AprendicesTmpImport;
use Maatwebsite\Excel\Facades\Excel;
class pruebaController extends Controller
{
    public function import(){
        Excel::import(new AprendicesImport,  request()->file('documento')) ;
        return response()->json(['message'=>'ya no joda mas']);
        return response()->json(['error'=>'mensaje de error']);
    }
}
