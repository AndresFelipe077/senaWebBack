<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = "empresa";

    const RUTA_LOGO_DEFAULT = "/default/logo.jpg";

    protected $appends = ['rutaLogoUrl'];

    public function getRutaLogoUrlAttribute()
    {
        if (
            isset($this->attributes['rutaLogo']) &&
            isset($this->attributes['rutaLogo'][0])
        ) {
            return url($this->attributes['rutaLogo']);
        }
        return url(self::RUTA_LOGO_DEFAULT);
    }

    public function roles()
    {
        return $this->hasMany(Rol::class, 'idCompany');
    }

    public function activacionUser()
    {
        return $this->hasMany(ActivationCompanyUser::class);
    }

}
