<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\estadoRap; 

class EstadoRapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
        public function run()
    {
        estadoRap::create(['nombreEstado' => 'PENDIENTE']);
        estadoRap::create(['nombreEstado' => 'APROBADO']);
        estadoRap::create(['nombreEstado' => 'CURSANDO']);
    }
}
