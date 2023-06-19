<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionRapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracionRap', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idParticipante');
            $table->unsignedInteger('idEstado');
            $table->unsignedInteger('idRap');
            $table->unsignedBigInteger('idJornada');
            $table->date('fechaInicial');
            $table->date('fechaFinal');

            $table->foreign('idParticipante')->references('id')->on('asignacionParticipante');
            $table->foreign('idRap')->references('id')->on('resultadoAprendizaje');
            $table->foreign('idEstado')->references('id')->on('estadoRap');
            $table->foreign('idJornada')->references('id')->on('jornada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracionRap');
    }
}
