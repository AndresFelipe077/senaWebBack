<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    public static $snakeAttributes = false;
    protected $table = "asistencia";
    protected $fillable =[
       'id',
       'idConfiguracionRap',
       'idAsignacionParticipante',
       'asistencia',
       'horaLlegada',
       'numberSesion',
       'fecha'
    ];
    public $timestamps = false;

    public function configuracionRap()
    {
        return $this->belongsTo(ConfiguracionRap::class,'idConfiguracionRap');
    }

    public function asignacionParticipante()
    {
        return $this->belongsTo(AsignacionParticipante::class, 'idAsignacionParticipante');
    }
}
