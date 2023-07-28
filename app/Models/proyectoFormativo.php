<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class proyectoFormativo extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "proyectoFormativo";
    protected $fillable = [
    "nombre",
    "codigo",
    "idPrograma",
    "tiempoEstimado",
    "numeroTotalRaps",
    "idCentroFormacion"
    ];

    public $timestamps = false;

    //realcion uno a muchos con programa
    public function programas()
    {
        return $this->belongsTo(Programa::class, 'idPrograma');
    }

    //relacion muchos a muchos con fase
    public function fases()
    {
        return $this->belongsToMany(
            Fase::class,
            asignacionFaseProyFormativo::class,
            'idFase', 'idProyectoFormativo'
        )->withPivot('id');
            
    }

    public function centroFormativos()
    {
        return $this->belongsTo(CentroFormacion::class, 'idCentroFormacion');
    }
    public function asignacionCompetencias()
    {
        return $this->belongsToMany(Competencias::class, 'asignacionCompetenciaProyecto', 'idProyecto', 'idCompetencia');
    }
}
