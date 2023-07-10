<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;
    protected $table = "regional";
    protected $fillable = [
        "nombreRegional",
        "descripcion"
    ];

    public $timestamps = false;
}
