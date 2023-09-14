<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jornada extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "jornada";
    protected $fillable = [
        'id',
        'nombreJornada',
        'descripcion',
        'horaInicial',
        'horaFinal',
        'numeroHoras'
    ];

    public function diaJornada()
    {
        return $this->belongsToMany(Dia::class, 'asignacionDiaJornada', 'idJornada', 'idDia');
    }

    public function grupo(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Grupo', 'asignacionJornadaGrupo', 'idJornada', 'idGrupo');
    }

    public function actividadEvento(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\ActividadEvento', 'asignacionJornadaActividadEvento', 'idJornada', 'idActividadEvento');
    }

}
