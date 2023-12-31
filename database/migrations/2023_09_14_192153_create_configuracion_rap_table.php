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
            $table->unsignedInteger('idRap');
            $table->unsignedInteger('idInstructor')->nullable();
            $table->unsignedBigInteger('idJornada')->nullable();
            $table->unsignedInteger('idGrupo');
            $table->unsignedInteger('idInfraestructura')->nullable();
            $table->unsignedInteger('idEstado');
            $table->integer('horas');
            $table->date('fechaInicial')->nullable();
            $table->date('fechaFinal')->nullable();
            $table->text('observacion')->nullable();

            $table->foreign('idRap')->references('id')->on('resultadoAprendizaje');
            $table->foreign('idInstructor')->references('id')->on('usuario');
            $table->foreign('idJornada')->references('id')->on('jornada');
            $table->foreign('idGrupo')->references('id')->on('grupo');
            $table->foreign('idInfraestructura')->references('id')->on('infraestructura');
            $table->foreign('idEstado')->references('id')->on('estadoConfiguracionRap');
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
