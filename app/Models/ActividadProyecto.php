<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadProyecto extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "actividadProyecto";
    protected $fillable = [
    "nombreActividadProyecto",
    "codigoAP",
    "idFaseProyecto"
    ];
    public $timestamps = false;

    public function faseProyecto()
    {
        return $this->belongsTo(asignacionFaseProyFormativo::class, 'idFaseProyecto');
    }

}
