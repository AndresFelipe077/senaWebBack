<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competencias extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "competencias";

    protected $fillable = [
        "nombreCompetencia",
        "codigoCompetencia",
        "idActividadProyecto",
      
    ];

    public $timestamps =false;


    //relacion uno a muchos
    public function actividadProyecto()
    {
        return $this->belongsTo(ActividadProyecto::class, 'idActividadProyecto');
    }
    
    //relacion muchos a  muchos
    public function resultados()
{
    return $this->belongsToMany(ResultadoAprendizaje::class, 'asignacionCompetenciasRaps', 'idCompetencia', 'idRap');
}

public function resultadosAprendizaje()
{
    return $this->hasMany(resultadoAprendizaje::class, 'idCompetencia', 'id');
}

}

