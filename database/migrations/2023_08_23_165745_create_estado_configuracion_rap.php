<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEstadoConfiguracionRap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estadoConfiguracionRap', function (Blueprint $table) {
            $table->increments('id');
            $table->string('estado');
            $table->timestamps();
        });

        DB::table('estadoConfiguracionRap')->insert([
            ['estado' => 'EN EJECUCION'],
            ['estado' => 'PENDIENTE'],
            ['estado' => 'FINALIZADO'],
            ['estado' => 'CAMBIO DE INSTRUCTOR'],
            ['estado' => 'PAUSADO']
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estadoConfiguracionRap');
    }
}
