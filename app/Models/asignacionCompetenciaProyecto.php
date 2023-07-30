<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asignacionCompetenciaProyecto extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "asignacionCompetenciaProyecto"; 
    protected $fillable = [
        'idCompetencia',
        'idProyecto'
    ];

    public $timestamps = false;


    public function competencias(){
        return $this->belongsTo(competencias::class,'idCompetencia');
    }

    public function proyectosFormativos(){
        return $this->belongsTo(proyectoFormativo::class, 'idProyecto');
    }
}
