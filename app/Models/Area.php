<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'area';
    protected $guarded = [];

    public function infraestructuras(){
        return $this -> hasMany(Infraestructura::class,'idArea');
    }
}
