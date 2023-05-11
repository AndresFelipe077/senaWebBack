<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infraestructura extends Model
{
    use HasFactory;

    protected $table = 'infraestructura';
    protected $guarded = [];

    public function sede(){
        return $this -> belongsTo(Sede::class, 'idSede');
    }
    public function area(){
        return $this -> belongsTo(Area::class,'idArea');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class)
            ->using(HorarioInfraestructuraGrupo::class)
            ->withPivot(['fechaInicial', 'fechaFinal']);
    }

}
