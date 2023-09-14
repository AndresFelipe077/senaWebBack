<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Programa;
use App\Models\proyectoFormativo;
use App\Models\TipoProgramas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeders/sql/tipo_competencia.sql';
        DB::unprepared(file_get_contents($path));

        $path = 'database/seeders/sql/jornada.sql';
        DB::unprepared(file_get_contents($path));

        $path = 'database/seeders/sql/estado_grupo_infraestructura.sql';
        DB::unprepared(file_get_contents($path));

        $path = 'database/seeders/sql/countries.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/cities.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/statuses.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/identification_types.sql';
        DB::unprepared(file_get_contents($path));
        // $path = 'database/seeders/sql/datosTipoTransaccion.sql';
        // DB::unprepared(file_get_contents($path));
        // $path = 'database/seeders/sql/medio_pago.sql';
        // DB::unprepared(file_get_contents($path));
        // $path = 'database/seeders/sql/tipo_pago.sql';
        // DB::unprepared(file_get_contents($path));

        // // tipo participantes
        // $path = 'database/seeders/sql/tipo_participante.sql';
        // DB::unprepared(file_get_contents($path));
        // // estadoParticipantes
        // $path = 'database/seeders/sql/estado_participante.sql';
        // DB::unprepared(file_get_contents($path));


        // TipoProgramas::factory(5)->create();
        $path = 'database/seeders/sql/tipo_programa.sql';
        DB::unprepared(file_get_contents($path));
        // Programa::factory(5)->create();

        $this->call(CompanySeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PersonSeeder::class);

        $path = 'database/seeders/sql/regionales.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/centroFormacion.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/sedes.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/areas.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/infraestructuras.sql';
        DB::unprepared(file_get_contents($path));

        Programa::factory(1)->create();


        $this->call(DiaSeeder::class);

        // $path = 'database/seeders/sql/tipo_programa.sql';
        // DB::unprepared(file_get_contents($path));
        // $path = 'database/seeders/sql/fases.sql';
        // DB::unprepared(file_get_contents($path));

        $path = 'database/seeders/sql/persona.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/usuario.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/activated_users.sql';
        DB::unprepared(file_get_contents($path));



        $path = 'database/seeders/sql/tipo_grupo.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/estado_grupo.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/tipo_formacion.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/tipo_oferta.sql';
        DB::unprepared(file_get_contents($path));

        // proyectoFormativo::factory(5)->create();
        proyectoFormativo::factory(1)->create();

        // Grupo::factory(5)->create();

        // $path = 'database/seeders/sql/asignacion_participante.sql';
        // DB::unprepared(file_get_contents($path));
    }
}
