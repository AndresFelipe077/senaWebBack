<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAsignacionParticipantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignacionParticipante', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idParticipante')->nullable();
            $table->foreign('idParticipante')->references('id')->on('usuario')->onDelete('cascade');

            $table->unsignedInteger('idGrupo')->nullable();
            $table->foreign('idGrupo')->references('id')->on('grupo')->onDelete('cascade')->nullable();

            $table->foreignId('idTipoParticipacion')->references('id')->on('tipoParticipacion')->onDelete('cascade');

            $table->foreignId('idEstadoParticipantes')->references('id')->on('estadoParticipantes')->onDelete('cascade');



            $table->date('fechaInicial')->nullable();

            $table->date('fechaFinal')->nullable(); //Mientras nullable para pruebas


             $table->text('observacion')->nullable();

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
        Schema::dropIfExists('asignacion_participantes');
    }
}
