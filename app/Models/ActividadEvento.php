<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActividadEvento extends Model
{
    use HasFactory;

    protected $table = "actividadEvento";

    protected $guarded = [];

    public function infraestructura(): BelongsTo
    {
        return $this->belongsTo(Infraestructura::class, 'idInfraestructura');
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(AsignacionParticipante::class, 'idParticipante');
    }

    //relacion con los grupos jornada pertenecientes a un grupo
    public function jornadas()
    {
        return $this->belongsToMany(
            Jornada::class,
            AsignacionJornadaActividadEvento::class,
            'idActividadEvento', 'idJornada'
        ) -> withPivot('id');
    }

}
