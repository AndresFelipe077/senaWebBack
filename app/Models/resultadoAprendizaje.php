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

    //relacion muchos a muchos
    public function competencias()
    {
        return $this->belongsTo(Competencias::class, 'idCompetencia');
    }

}
