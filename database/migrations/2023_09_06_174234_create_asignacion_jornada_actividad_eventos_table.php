<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsignacionJornadaActividadEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignacionJornadaActividadEvento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('idActividadEvento')->references('id')->on('actividadEvento')->onDelete('cascade');

            $table->unsignedBigInteger('idJornada');
            $table->foreign('idJornada')->references('id')->on('jornada')->onDelete('cascade');

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
        Schema::dropIfExists('asignacion_jornada_actividad_eventos');
    }
}
