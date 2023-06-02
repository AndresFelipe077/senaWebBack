<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionCompetenciaRap extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "asignacionCompetenciasRaps"; 
    protected $fillable = [
        'idCompetencia',
        'idRap'
    ];

    public $timestamps = false;


    public function competencias(){
        return $this->belongsTo(competencias::class);
    }

    public function resultadoAprendizaje(){
        return $this->belongsTo(resultadoAprendizaje::class, 'idRap');
    }

}
