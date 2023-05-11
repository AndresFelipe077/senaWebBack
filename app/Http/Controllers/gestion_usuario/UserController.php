<?php

namespace App\Http\Controllers\gestion_usuario;

use App\Http\Controllers\Controller;
use App\Models\ActivationCompanyUser;
use App\Models\Person;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session as FacadesSession;

class UserController extends Controller
{
    public function getUsers()
    {
        $id = FacadesSession::get("company_id");
        $user = ActivationCompanyUser::with('company', 'user', 'user.persona', 'roles', 'estado')
            ->where('company_id', $id)
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


    public function asignation(Request $request)
    {

        DB::table('model_has_roles')
            ->where('model_id', $request->idActivation)
            ->delete();
        $user = ActivationCompanyUser::find($request->input('idActivation'));
        $user->assignRole($request->input('roles', []));
        return $user;
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function destroy(int $id)
    {
        ActivationCompanyUser::where('user_id', $id)->delete();
        $user = User::findOrFail($id);
        $idPersona = $user->idpersona;
        User::where('id', $id)->delete();
        Person::where('id', $idPersona)->delete();

        return response()->json([], 204);
    }
}
