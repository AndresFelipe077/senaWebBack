<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Company::factory()->create([
            'razonSocial' => "Sena",
            'rutaLogo' => '/default/logoSena.png'

        ]);

        // \App\Models\Company::factory(10)->create();
    }
}
