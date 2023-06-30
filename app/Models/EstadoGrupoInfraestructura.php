<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoGrupoInfraestructura extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'estadoGrupoInfraestructura';


    public function grupos(){
        return $this->hasMany(HorarioInfraestructuraGrupo::class, 'idEstado');
    }

}
