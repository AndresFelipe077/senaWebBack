<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        return $this->belongsToMany('App\Models\Dia', 'asignacionDiaJornada', 'idJornada', 'idDia');
    }

    public function actividadEventos(): HasMany
    {
        return $this->hasMany(ActividadEvento::class, 'idJornada');
    }

}
