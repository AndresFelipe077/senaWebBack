<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = "grupo";

    protected $guarded = [];

    //Relacion uno a muchos Inversa(TipoGrupos->Grupo)
    public function tipoGrupo()
    {
        return $this->belongsTo(TipoGrupo::class, 'idTipoGrupo', 'id');
    }

    //Relacion uno a muchos Inversa(programa->Grupo)
    public function proyectoFormativo()
    {
        return $this->belongsTo(proyectoFormativo::class, 'idProyectoFormativo', 'id');
    }

    //relacion con horarioInfraestructuraGrupo pertenecientes a grupo con infraestructura
    public function infraestructuras()
    {
        return $this->belongsToMany(
            Infraestructura::class,
            HorarioInfraestructuraGrupo::class,
            'idGrupo', 'idInfraestructura'
        ) -> withPivot('id','fechaInicial', 'fechaFinal');
    }

    //Relacion uno a muchos Inversa(nivelFormacion->Grupo)
    public function nivelFormacion()
    {
        return $this->belongsTo(NivelFormacion::class, 'idNivel', 'id');
    }

    //Relacion uno a muchos Inversa(tipoFormacion->Grupo)
    public function tipoFormacion()
    {
        return $this->belongsTo(TipoFormacion::class, 'idTipoFormacion', 'id');
    }

    //Relacion uno a muchos Inversa(EstadoGrupo->Grupo)
    public function estadoGrupo()
    {
        return $this->belongsTo(EstadoGrupo::class, 'idEstado');
    }

    //Relacion uno a muchos Inversa(tipoOferta->Grupo)
    public function tipoOferta()
    {
        return $this->belongsTo(TipoOferta::class, 'idTipoOferta', 'id');
    }

    //relacion con los grupos jornada pertenecientes a un grupo
    public function jornadas()
    {
        return $this->belongsToMany(
            Jornada::class,
            AsignacionJornadaGrupo::class,
            'idGrupo', 'idJornada'
        ) -> withPivot('id');
    }




    // utilizando vanesaaaa

    //relacion con AsignacionParticipante pertenecientes a un grupo
    public function participantes()
    {
        return $this->belongsToMany(User::class, AsignacionParticipante::class, 'idGrupo', 'idParticipante');
    }





    public function asignacionParticipantes()
    {
        return $this->belongsToMany(AsignacionParticipante::class, 'asignacionParticipante', 'idGrupo', 'id')->withTimestamps();
    }



}
