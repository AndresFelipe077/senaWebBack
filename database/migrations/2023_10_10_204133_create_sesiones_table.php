<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSesionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idConfiguracionRap');
            $table->date('fecha');
            $table->boolean('asistencia');
            $table->time('horaLlegada');
            $table->integer('numberSesion');

            $table->foreign('idConfiguracionRap')->references('id')->on('configuracionrap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sesiones');
    }
}
