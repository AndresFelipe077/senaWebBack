<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class resultadoAprendizaje extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "resultadoAprendizaje";
    protected $fillable = [
        "rap",
        "codigoRap",
        "numeroHoras",
        "idTipoRaps",
        "idCompetencia"
    ];
    public $timestamps = false;


    // relacion uno a muchos entre raps y tipo raps

    public function tipoRaps()
    {
        return $this->belongsTo(TipoRaps::class, 'idTipoRaps');
    }

    public function actividadesAprendizaje()
    {
        return $this->hasMany(actividadAprendizaje::class,'rap');
    }


    
    //relacion muchos a muchos
    public function competencias()
    {
        return $this->belongsToMany(Competencias::class, 'asignacionCompetenciasRaps', 'idRap', 'idCompetencia');
    }




    // Relación con el modelo Competencias
    public function competencia()
    {
        return $this->belongsTo(Competencias::class, 'idCompetencia');
    }
    public function tableCompetencia()
    {
        return $this->belongsTo(Competencias::class, 'competencias');
    }
}
