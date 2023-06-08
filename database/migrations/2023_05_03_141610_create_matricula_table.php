<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatriculaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matricula', function (Blueprint $table) {
            $table->increments('id');
             $table->unsignedInteger('idGrupo');
            $table->foreign('idGrupo')->references('id')->on('grupo');

            $table->foreignId('idEstadoGrupo')->references('id')->on('estadoGrupo');
            $table->unsignedInteger('idPersona');
            $table->foreign('idPersona')->references('id')->on('persona');

            
            $table->foreignId('idJornada')->references('id')->on('jornada');
            $table->unsignedInteger('idProyectoFormativo');
            $table->foreign('idProyectoFormativo')->references('id')->on('proyectoFormativo');

            $table->date('fechaInicial');

            $table->date('fechaAceptacion');

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
        Schema::dropIfExists('matricula');
    }
}
