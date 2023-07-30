<?php

namespace App\Http\Controllers\gestion_usuario;

use App\Http\Controllers\Controller;
use App\Models\ActivationCompanyUser;
use App\Models\Person;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session as FacadesSession;

class UserController extends Controller
{
    public function index()
    {
        // $id = FacadesSession::get("company_id");
        $user = ActivationCompanyUser::with('company', 'user', 'user.persona', 'roles', 'estado')
            // ->where('company_id', $id)
            ->get();

        return response()->json($user);
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $persona = new Person($data);
        $persona->rutaFoto = Person::RUTA_FOTO_DEFAULT;
        $persona->identificacion = rand(0, 99999);
        $persona->save();

        $usuario = new User($data);
        $usuario->contrasena = bcrypt($request->input('contrasena'));
        $usuario->idpersona = $persona->id;
        $usuario->save();

        $activacion = new ActivationCompanyUser();
        $activacion->user_id = $usuario->id;
        $activacion->state_id = 1;
        $activacion->company_id = FacadesSession::get("company_id");
        $activacion->fechaInicio = date('Y-m-d');
        $activacion->fechaFin = date('Y-m-d');
        $activacion->save();

        return response()->json($usuario, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $persona = Person::find($id);
        $persona->fill($data);
        $persona->save();

        $usuario = User::where('idpersona', $persona->id)->first();
        $usuario->fill($data);
        if ($request->has('contrasena')) {
            $usuario->contrasena = bcrypt($request->input('contrasena'));
        }
        $usuario->save();

        return response()->json($usuario, 200);
    }


    public function asignation(Request $request, $id)
    {
    
        $roleIds = (array) $request->input('roles', []);
    
        // Buscar los roles por sus id
        $roles = Rol::whereIn('id', $roleIds)->pluck('name')->toArray();
    
        // Asignar los roles al usuario
        $user = ActivationCompanyUser::find($id);
        $userRoles = $user->getRoleNames()->toArray();
    
        // Agregar los nuevos roles a los roles existentes del usuario
        $roles = array_merge($userRoles, $roles);
    
        // Asignar los roles actualizados al usuario
        $user->syncRoles($roles);
    
        return response()->json(['message' => 'Roles asignados correctamente'], 200);
    }

    public function filtrarRolesAsignados($id)
    {
        $activationCompanyUser = ActivationCompanyUser::with('roles')->find($id);
    
        
        if (!$activationCompanyUser) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // se utiliza funcion para traer los roles que estan en activationCompanyUser
        $assignedRoles = $activationCompanyUser->roles;
    
        
        $allRoles = Rol::all();
    
        // se crea filtro para obtener los filtros que no estan en activationCompanyUser
        $unassignedRoles = $allRoles->diff($assignedRoles);
    
       
        return response()->json([
            'assigned_roles' => $assignedRoles,
            'unassigned_roles' => $unassignedRoles
        ]);
    }

    public function destroy(int $id)
    {
        // Obtén el ActivationCompanyUser
        $activationCompanyUser = ActivationCompanyUser::where('user_id', $id)->first();
        
        // Si el ActivationCompanyUser existe, desasigna los roles
        if($activationCompanyUser) {
            $activationCompanyUser->syncRoles([]); // Desasigna todos los roles
            $activationCompanyUser->delete();
        }
    
        // Obtén el User
        $user = User::findOrFail($id);
        $idPersona = $user->idpersona;
    
        // Elimina el User
        User::where('id', $id)->delete();
    
        // Elimina el Person
        Person::where('id', $idPersona)->delete();
    
        return response()->json(['message' => 'Roles elominados correctamente'], 204);
    }

    public function unassignRoles(Request $request, $id)
    {
        $roleIds = (array) $request->input('roles', []);
        
        // Buscar los roles por sus id
        $roles = Rol::whereIn('id', $roleIds)->get();
        
        // Obtener el usuario
        $user = ActivationCompanyUser::find($id);
        
        // Quitar los roles especificados del usuario
        foreach($roles as $role) {
            $user->removeRole($role->name);
        }
        
        return response()->json(['message' => 'Roles desasignados correctamente'], 200);
    }
}
