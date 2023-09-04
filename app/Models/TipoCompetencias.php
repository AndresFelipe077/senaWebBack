<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCompetencias extends Model
{
    use HasFactory;
    
    public static $snakeAttributes = false;
    protected $table = "tipoCompetencias";
    protected $fillable = [
        "nombre",
        "codigo"
    ];
    public $timestamps = false;
}
