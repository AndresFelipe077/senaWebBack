<?php

namespace Database\Seeders;

use App\Http\Middleware\Permisions;
use App\Models\ActivationCompanyUser;
use App\Models\Person;
use App\Models\User;
use App\Permission\PermissionConst;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vt = new Role();
        $vt->name = "Admin";
        $vt->idCompany = 1;
        $vt->save();


        $vt->syncPermissions([
            PermissionConst::GESTION_ROL_PERMISOS,
            PermissionConst::GESTION_ROLES,
            PermissionConst::GESTION_TIPO_CONTRATO,
            PermissionConst::GESTION_USUARIO,
            PermissionConst::GESTION_PROCESOS,
            PermissionConst::GESTION_TIPO_CONTRATO,
            PermissionConst::GESTION_MEDIO_PAGO,
            PermissionConst::GESTION_TIPO_PAGO,
            PermissionConst::GESTION_TIPO_TRANSACCION,
            PermissionConst::GESTION_TIPO_DOCUMENTOS,
            PermissionConst::GESTION_TIPO_PROGRAMAS,
            PermissionConst::GESTION_PROGRAMAS,
            PermissionConst::GESTION_FASES,
            PermissionConst::GESTION_ACTIVIDAD_PROYECTO,
            PermissionConst::GESTION_PROYECTO_FORMATIVO,



// permisao vanesas
            PermissionConst::GESTION_COMPETENCIA,
            PermissionConst::GESTION_ACTIVIDAD_APRENDIZAJE,
            PermissionConst::GESTION_RESULTADO_APRENDIZAJE,



            PermissionConst::GESTION_PAGO_NOMINA,
            PermissionConst::GESTION_GRUPO,
            PermissionConst::GESTION_TIPO_GRUPO,

            PermissionConst::GESTION_AREA,
            PermissionConst::GESTION_SEDE,
            PermissionConst::GESTION_INFRAESTRUCTURA,

            PermissionConst::GESTION_JORNADA,
            PermissionConst::CALENDARIO,
            PermissionConst::GESTION_MATRICULA,
            PermissionConst::GESTION_FICHAS,

            PermissionConst::GESTION_CRITERIOS_EVALUACION
        ]);

        $emailAdmin = "admin@gmail.com";
        Person::factory()
            ->hasUsuario(1, ['email' => $emailAdmin])
            ->create([
                'email'  => $emailAdmin,
            ]);


        $activation = ActivationCompanyUser::factory()->create([
            'company_id' => 1,
            'user_id' => 1,
            'state_id' => 1
        ]);

        $activation->assignRole($vt);

    }
}
