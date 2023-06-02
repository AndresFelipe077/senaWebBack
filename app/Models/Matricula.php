<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "matricula";
    protected $guarded = [
    ];

    public $timestamps = false;

    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo');
    }
    public function EstadoGrupo()
    {
        return $this->belongsTo(EstadoGrupo::class, 'idEstadoGrupo');
    }

    public function Persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona');
    }
    public function ProyectoFormativo()
    {
        return $this->belongsTo(ProyectoFormativo::class, 'idProyectoFormativo');
    }
    

}
