<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    use HasFactory;
    
    public static $snakeAttributes = false;
    protected $table = "fase";
    protected $fillable = [
        "nombreFase",
        "codigoFase"
    ];
    public $timestamps = false;

    //relacion muchos a muchos con fase
    public function proyectos()
    {
        return $this->belongsToMany(
            proyectoFormativo::class,
            asignacionFaseProyFormativo::class,
            'idFase', 'idProyectoFormativo'
        )->withPivot('id');
    }

    public function proyectosFormativos()
    {
        return $this->belongsToMany(proyectoFormativo::class, 'asignacionFaseProyecto');
    }
    
}
