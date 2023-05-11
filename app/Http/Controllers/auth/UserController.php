<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\ActivationCompanyUser;
use App\Models\Person;
use App\Models\Rol;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
  public function __construct()
  {
  }

  public function logged()
  {

    $response = Session::get('usuario');
    return response($response);
  }

  public function setCompany($idUserActive)
  {
    $id = auth()->id();

    $userActivate = ActivationCompanyUser::with('company')
      ->active()
      ->byUser($id)
      ->findOrFail($idUserActive);

    $permissionsName = $this->permissionsToString($userActivate->getAllPermissions());

    $response = new \stdClass();
    $response->user = Person::where('id', auth()->user()->idpersona)->first();
    $response->permission = $permissionsName;
    $response->userActivate = $userActivate;

    Session::put('company_id', $userActivate->company_id);
    Session::put('user_activate_id', $userActivate->id);
    Session::put('permissions', $permissionsName);
    Session::put('usuario', json_encode($response));
  }

  private function permissionsToString($permissions)
  {
    $permissions = collect($permissions)->map(function ($permission) {
      return $permission->name;
    });
    return implode(',', $permissions->toArray());
  }

  public function instructores()
  {
    $usuariosActivados = ActivationCompanyUser::role('INSTRUCTOR')->active()->get();

    $usuariosActives = collect();
    foreach ($usuariosActivados as $usuario) {
      $usuarioActive = $usuario->toArray();
      $usuarioActive['usuario'] = Person::find($usuario->user_id);
      $usuariosActives->push($usuarioActive);
    }

    return response()->json($usuariosActives);
  }
  
}
