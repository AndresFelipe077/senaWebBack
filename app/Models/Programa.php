<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;

    public static $snakeAttributes = false;
    protected $table = "programa";

const RUTA_DOC = "programa";
const RUTA_DOC_DEFAULT = "default";
protected $appends = ['docUrl'];

    protected $fillable = [
    "nombrePrograma",
    "codigoPrograma",
    "descripcionPrograma",
    "idTipoPrograma",
    "idEstado",
    "totalHoras",
    "etapaLectiva",
    "etapaProductiva",
    "creditosLectiva",
    "creditosProductiva",
    "rutaArchivo"
    ];
    public $timestamps = false;

 public function getDocUrlAttribute(){
    if(
        isset($this->attributes['rutaArchivo']) &&
        isset($this->attributes['rutaArchivo'][0])
    ){
        return url($this->attributes['rutaArchivo']);
    }
    return url(self::RUTA_DOC_DEFAULT);
 }


    //relacion uno a muchos tipo programa
    public function tipoPrograma()
    {
        return $this->belongsTo(TipoProgramas::class, 'idTipoPrograma');
    }
    
    //relacion uno a muchos estado
    public function estado()
    {
        return $this->belongsTo(Status::class, 'idEstado');
    }
}
