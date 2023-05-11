<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo', function (Blueprint $table) {

            $table->increments('id');
            $table->string('nombre');
            $table->date('fechaInicialGrupo');
            $table->date('fechaFinalGrupo');
            $table->text('observacion');

            $table->foreignId('idTipoGrupo')->references('id')->on('tipoGrupo');

            $table->unsignedInteger('idLider')->nullable();
            $table->foreign('idLider')->references('id')->on('usuario');

            $table->unsignedInteger('idPrograma');
            $table->foreign('idPrograma')->references('id')->on('programa');

            $table->foreignId('idNivel')->references('id')->on('nivelFormacion');

            $table->foreignId('idTipoFormacion')->references('id')->on('tipoFormacion');

            $table->foreignId('idEstado')->references('id')->on('estadoGrupo');

            $table->foreignId('idTipoOferta')->references('id')->on('tipoOferta');

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
        Schema::dropIfExists('grupo');
    }
}
