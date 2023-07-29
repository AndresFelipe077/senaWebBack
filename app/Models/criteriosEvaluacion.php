<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class criteriosEvaluacion extends Model
{
    use HasFactory;
    protected $table = 'criteriosEvaluacion';
    
        protected $fillable = ['codigo', 'descripcion'];
        
        
        protected $visible = ['id', 'codigo', 'descripcion'];
    
    protected $guarded = [];


    public function planeaciones()
    {
        return $this->belongsToMany(Planeacion::class, 'asociacionCriteriosPlaneacion', 'id_criterioEvaluacion', 'id_planeacion');
    }
    
}
