<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistencia', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idconfiguracionRap');
            $table->unsignedBigInteger('idasignacionParticipante');
            $table->boolean('asistencia');
            $table->time('horaLlegada');
            $table->integer('numberSesion');
            $table->date('fecha');

            $table->foreign('idConfiguracionRap')->references('id')->on('configuracionrap');
            $table->foreign('idAsignacionParticipante')->references('id')->on('asignacionParticipante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asistencia');
    }
}
