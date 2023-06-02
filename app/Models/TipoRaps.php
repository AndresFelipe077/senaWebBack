<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoRaps extends Model
{
    use HasFactory;
    
    public static $snakeAttributes = false;
    protected $table = "tipoRaps";
    protected $fillable = [
        "nombre",
        "codigo"
    ];
    public $timestamps = false;
}
