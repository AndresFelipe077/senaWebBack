<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionRap extends Model
{
    use HasFactory;

    public static $snakeAttributes = false;
    protected $table = "configuracionRap";
    protected $guarded = [];
    public $timestamps =false;

    //relaciones
    public function resultados(){
        return $this->belongsTo(resultadoAprendizaje::class, 'idRap');
    }

    public function usuarios(){
        return $this->belongsTo(User::class, 'idInstructor');
    }

    public function estados(){
        return $this->belongsTo(estadoConfiguracionRap::class, 'idEstado');
    }

    public function jornadas(){
        return $this->belongsTo(Jornada::class, 'idJornada');
    }

    public function infraestructuras(){
        return $this->belongsTo(Infraestructura::class, 'idInfraestructura');
    }
    
    public function grupos(){
        return $this->belongsTo(Grupo::class, 'idGrupo');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'idConfiguracionRap');
    }

}
