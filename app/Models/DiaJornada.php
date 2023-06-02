<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaJornada extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "asignacionDiaJornada";
    protected $fillable = [
        'idJornada',
        'idDia'
    ];
    // public $timestamps = false;

    public function jornada()
    {
        return $this->belongsTo('App\Models\Jornada');
    }

    public function dia()
    {
        return $this->belongsTo('App\Models\Dia');
    }
}
