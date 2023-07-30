<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asignacionFaseProyFormativo extends Model
{
    use HasFactory;
    
    protected $guarded = [];


    protected $table = 'asignacionFaseProyecto';


   
    public function fase()
    {
        return $this->belongsTo(Fase::class, 'idFase');
    }

    public function proyectoFormativo()
    {
        return $this->belongsTo(proyectoFormativo::class, 'idProyectoFormativo');
    }

    
}
