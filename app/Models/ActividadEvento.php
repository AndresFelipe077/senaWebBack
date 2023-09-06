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

    public function participantes(): BelongsTo
    {
        return $this->belongsTo(AsignacionParticipante::class, 'idParticipante');
    }

    public function jornada(): BelongsTo
    {
        return $this->belongsTo(Jornada::class, 'idJornada');
    }

}
