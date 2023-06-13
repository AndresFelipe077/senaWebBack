<?php

namespace Database\Seeders;

use App\Permission\PermissionConst;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->savePermission(PermissionConst::GESTION_ROLES, "Gestión de roles");
        $this->savePermission(PermissionConst::GESTION_ROL_PERMISOS, "Gestión permisos del rol");
        $this->savePermission(PermissionConst::GESTION_USUARIO, "Gestión de usuarios");
        $this->savePermission(PermissionConst::GESTION_TIPO_CONTRATO, "Gestión de tipos de contrato");
        $this->savePermission(PermissionConst::GESTION_PROCESOS, "Gestión de procesos");
        $this->savePermission(PermissionConst::GESTION_TIPO_DOCUMENTOS, "Gestión de tipos de documento");
        $this->savePermission(PermissionConst::GESTION_MEDIO_PAGO, "Gestión medios de pago");
        $this->savePermission(PermissionConst::GESTION_TIPO_PAGO, "Gestión de tipos de pago");
        $this->savePermission(PermissionConst::GESTION_TIPO_TRANSACCION, "Gestión de tipos de transacciòn");

// realizado por vanesa
        $this->savePermission(PermissionConst::GESTION_COMPETENCIA, "Gestión de competencia");
        $this->savePermission(PermissionConst::GESTION_ACTIVIDAD_APRENDIZAJE, "Gestión actividad aprendizaje");
        $this->savePermission(PermissionConst::GESTION_RESULTADO_APRENDIZAJE, "Gestión resultado aprendizaje");


        $this->savePermission(PermissionConst::GESTION_SEDE, "Gestión de sedes");
        $this->savePermission(PermissionConst::GESTION_AREA, "Gestión de areas");
        $this->savePermission(PermissionConst::GESTION_INFRAESTRUCTURA, "Gestión de infraestructuras");

        $this->savePermission(PermissionConst::GESTION_TIPO_PROGRAMAS, "Gestión de tipos de competencias");
        $this->savePermission(PermissionConst::GESTION_PROGRAMAS, "Gestión de tipos de programas");



        $this->savePermission(PermissionConst::GESTION_FICHAS, "Gestión Fichas");



        $this->savePermission(PermissionConst::GESTION_FASES, "Gestión de fases");
        $this->savePermission(PermissionConst::GESTION_ACTIVIDAD_PROYECTO, "Gestión de actividad de proyecto");
        $this->savePermission(PermissionConst::GESTION_PROYECTO_FORMATIVO, "Gestión de proyecto formativo");
        $this->savePermission(PermissionConst::GESTION_JORNADA, "Gestion de Jornada");
        $this->savePermission(PermissionConst::GESTION_PAGO_NOMINA, "Gestión de pagos de Nomina");
        $this->savePermission(PermissionConst::GESTION_GRUPO, "Gestion de grupos");// se crea permiso en archivo seeder
        $this->savePermission(PermissionConst::GESTION_TIPO_GRUPO, "Gestion de tipos de grupos");// se crea permiso en archivo seeder
        $this->savePermission(PermissionConst::CALENDARIO, "Calendario");
        $this->savePermission(PermissionConst::REGIONAL, "Regional");
        $this->savePermission(PermissionConst::CENTRO_FORMACION, "gestion de centros de formacion");
        $this->savePermission(PermissionConst::GESTION_MATRICULA,"Gestión de matricula");


    }

    private function savePermission($name, $description)
    {
        $permission = new Permission();
        $permission->name = $name;
        $permission->description = $description;
        $permission->save();
    }

}
