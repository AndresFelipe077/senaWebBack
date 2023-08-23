<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesiones extends Model
{
    use HasFactory;

    public static $snakeAttributes = false;
    protected $table = "sesiones";
    protected $fillable =[
       'id',
       'idConfiguracionRap',
       'fecha',
       'asistencia',
       'horaLlegada',
       'numberSesion'
    ];
    public $timestamps = false;

    public function configuracionRap()
    {
        return $this->belongsTo(ConfiguracionRap::class, 'idConfiguracionRap');
    }

}
