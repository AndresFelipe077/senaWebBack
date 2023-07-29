<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsociacionCriteriosPlaneacion extends Model
{
    use HasFactory;
    protected $table = 'asociacionCriteriosPlaneacion';
    protected $fillable = ['id', 'id_planeacion', 'id_criterioEvaluacion'];
    protected $guarded = [];


    public function planeacion()
    {
        return $this->belongsTo(Planeacion::class, 'id_planeacion');
    }

    public function criterioEvaluacion()
    {
        return $this->belongsTo(ModelsCriteriosEvaluacion::class, 'id_criterioEvaluacion');
    }    
}
