<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planeacion extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "planeacion";
    protected $fillable = [
        "idResultadoAprendizaje",
        "idActividadProyecto",
        "horas"
    ];
    public $timestamps = false;


    public function actividadProyectos(){
        return $this->belongsTo(ActividadProyecto::class, 'idActividadProyecto');
    }

    public function resultados(){
        return $this->belongsTo(resultadoAprendizaje::class, 'idResultadoAprendizaje');
    }

    public function actividadAprendizajes(){
        return $this->hasMany(actividadAprendizaje::class, 'idPlaneacion');
    }

}
