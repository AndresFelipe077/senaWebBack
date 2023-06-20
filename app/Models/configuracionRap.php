<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracionRap extends Model
{
    use HasFactory;

    public static $snakeAttributes = false;
    protected $table = "configuracionRap";
    protected $fillable = [
        'id',
        'idRap',
        'idParticipante',
        'idEstado',
        'idJornada',
        'fechaInicial',
        'fechaFinal'
    ];
    public $timestamps =false;

    //relaciones
    public function resultados(){
        return $this->belongsTo(resultadoAprendizaje::class, 'idRap');
    }

    public function participantes(){
        return $this->belongsTo(AsignacionParticipante::class, 'idParticipante');
    }

    public function estados(){
        return $this->belongsTo(estadoRap::class, 'idEstado');
    }

    public function jornadas(){
        return $this->belongsTo(Jornada::class, 'idJornada');
    }
    

}
