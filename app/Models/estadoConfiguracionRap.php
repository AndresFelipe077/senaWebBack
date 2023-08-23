<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class estadoConfiguracionRap extends Model
{
    use HasFactory;

    public static $snakeAttributes = false;
    protected $table = "estadoConfiguracionRap";
    protected $fillable = [
        'id',
        'estado'
    ];
    public $timestamps =false;
}
