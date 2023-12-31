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
            $table->text('observacion')->nullable();

            $table->foreignId('idTipoGrupo')->references('id')->on('tipoGrupo');

            $table->unsignedInteger('idProyectoFormativo');
            $table->foreign('idProyectoFormativo')->references('id')->on('proyectoFormativo');

            $table->foreignId('idTipoFormacion')->references('id')->on('tipoFormacion');

            $table->foreignId('idEstado')->references('id')->on('estadoGrupo');

            $table->foreignId('idTipoOferta')->references('id')->on('tipoOferta');

            $table->string('imagenIcon')->nullable();
        
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
