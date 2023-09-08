<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionFaseProyFormativo extends Model
{
    use HasFactory;

    protected $table = 'asignacionFaseProyecto';
    protected $guarded = [];

    public function fase()
    {
        return $this->belongsTo(Fase::class, 'idFase');
    }

    public function proyectoFormativo()
    {
        return $this->belongsTo(proyectoFormativo::class, 'idProyectoFormativo');
    }
}
