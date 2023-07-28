<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class estadoRap extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "estadoRap";
    protected $fillable = [
        'id',
        'nombreEstado',
    ];
}
