<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use  HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $table = "usuario";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function persona()
    {
        return $this->belongsTo(Person::class, 'idpersona');
    }

    

    public function grupoLider()
    {
        return $this->hasOne(Grupo::class, 'idLider', 'id');
    }

    //relacion con los grupos Many To Many
    public function grupos()
    {
        return $this->belongsToMany(Grupo::class)
            ->using(AsignacionParticipante::class)
            ->withPivot(['fechaInicial', 'fechaFinal', 'descripcion']);
    }

    public function activacion()
    {
        return $this->hasMany(ActivationCompanyUser::class, 'user_id', 'id');
    }
    
}
