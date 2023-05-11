<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\TipoGrupo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Competencias;
use App\Models\EstadoGrupo;
use App\Models\Jornada;
use App\Models\NivelFormacion;
use App\Models\Programa;
use App\Models\TipoFormacion;
use App\Models\TipoOferta;
use App\Models\TipoProgramas;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        Jornada::factory(3)->create();

        $path = 'database/seeders/sql/countries.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/cities.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/statuses.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/identification_types.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/datosTipoTransaccion.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/medio_pago.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/tipo_pago.sql';
        DB::unprepared(file_get_contents($path));


        TipoProgramas::factory(10)->create();
        Programa::factory(10)->create();
      
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

        $this->call(DiaSeeder::class);

        $path = 'database/seeders/sql/tipo_grupo.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/estado_grupo.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/nivel_formacion.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/tipo_formacion.sql';
        DB::unprepared(file_get_contents($path));
        $path = 'database/seeders/sql/tipo_oferta.sql';
        DB::unprepared(file_get_contents($path));
        
        Grupo::factory(10)->create();

    }
}
