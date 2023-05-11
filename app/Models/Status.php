<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = "estado";

    const ID_ACTIVE = 1;
    const ID_INACTIVO = 2;


    public function activacionesCompany()
    {
        return $this->hasMany(ActivationCompanyUser::class);
    }

}
