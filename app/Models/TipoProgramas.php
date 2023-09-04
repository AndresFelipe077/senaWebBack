<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProgramas extends Model
{
    use HasFactory;
    
    public static $snakeAttributes = false;
    protected $table = "tipoPrograma";
    protected $fillable = [
        "nombreTipoPrograma",
        "descripcion"
    ];
    public $timestamps = false;

    public function programas()
    {
        return $this->hasMany(Programa::class, 'idTipoPrograma');
    }

}
