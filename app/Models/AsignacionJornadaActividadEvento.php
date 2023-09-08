<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionJornadaActividadEvento extends Model
{
    use HasFactory;

    protected $table = 'asignacionJornadaActividadEvento';

    protected $guarded = [];

    public function jornada(){
        return $this -> belongsTo(Jornada::class,'idJornada');
    }
    public function actividadEvento(){
        return $this -> belongsTo(ActividadEvento::class,'idActividadEvento');
    }

}
