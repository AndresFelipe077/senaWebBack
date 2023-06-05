<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //''''''''creacion de la tabla'''''''''//
    public function up()
    {
        Schema::create('competencias', function (Blueprint $table) {
            $table->increments('id');
            $table->text ('nombreCompetencia');
            $table->text ('codigoCompetencia');

            $table->foreign('idActividadProyecto')->references('id')->on('actividadProyecto');
            $table->unsignedInteger('idActividadProyecto');


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
        Schema::dropIfExists('competencias');
    }
    
}
