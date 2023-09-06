<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPrograma extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "estadoPrograma";
    protected $fillable = [
        'id',
        'estado'
    ];
}
