<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultadoAprendizajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resultadoAprendizaje', function (Blueprint $table) {
            $table->increments('id');
            $table->string ('rap',20);
            $table->string('codigoRap',50);
            $table->unsignedInteger('idTipoRaps');
            $table->unsignedInteger('idCompetencia');

            $table->foreign('idTipoRaps')->references('id')->on('tipoRaps');
            $table->foreign('IdCompetencia')->references('id')->on('competencias');


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
        Schema::dropIfExists('resultadoAprendizaje');
    }
}
