<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class actividadAprendizaje extends Model
{
    use HasFactory;

    public static $snakeAttributes = false;
    protected $table = "actividadAprendizaje";

    protected $fillable = [
        "NombreAA",
        "codigoAA",
        "idEstado",
        "idRap"
    ];

    public $timestamps =false;
    public function rap()
    {
        return $this->belongsTo(resultadoAprendizaje::class, 'idRap');
    }
    public function estado()
    {
        return $this->belongsTo(Status::class, 'idEstado');
    }



}


