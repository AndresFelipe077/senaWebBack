<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programa', function (Blueprint $table) {
            $table->increments('id');          
            $table->text('nombrePrograma');
            $table->text('codigoPrograma');
            $table->text('descripcionPrograma');
            $table->integer('totalHoras');
            $table->integer('etapaLectiva');
            $table->integer('etapaProductiva');
            $table->integer('creditosLectiva');
            $table->integer('creditosProductiva');
            $table->string ('rutaArchivo');
            $table->text ('version');
            $table->unsignedInteger('idTipoPrograma');
            $table->unsignedInteger('idEstado');
            $table->unsignedInteger('idArea');


            $table->foreign('idTipoPrograma')->references('id')->on('tipoPrograma');
            $table->foreign('idEstado')->references('id')->on('estadoPrograma');
            $table->foreign('idArea')->references('id')->on('area');

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
        Schema::dropIfExists('programa');
    }
}
