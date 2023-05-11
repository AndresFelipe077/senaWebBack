<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ActivationCompanyUser extends Model
{
    use HasFactory, HasRoles;

    protected $guard_name = "web";

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function estado()
    {
        return $this->belongsTo(Status::class, 'state_id');
    }

    public function scopeActive($query)
    {
        $now = \Carbon\Carbon::now();
        return $query
            ->where('state_id', Status::ID_ACTIVE)
            ->whereDate('fechaInicio', '<=', $now)
            ->whereDate('fechaFin', '>=', $now);
    }

    public function scopeByUser($query, $idUser)
    {
        return $query->where('user_id', $idUser);
    }
    
}
