<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioInfraestructuraGrupo extends Model
{
    use HasFactory;

    protected $table = 'horarioInfraestructuraGrupo';

    protected $guarded = [];

    public function infraestructura()
    {
        return $this->belongsTo(Infraestructura::class, 'idInfraestructura');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo');
    }


}
