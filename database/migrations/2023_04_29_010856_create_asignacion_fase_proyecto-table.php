<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsignacionFaseProyectoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignacionFaseProyecto', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('idFase');
            $table->unsignedInteger('idProyectoFormativo');
            $table->text ('descripcion')->nullable();

            $table->foreign('idProyectoFormativo')->references('id')->on('proyectoFormativo');
            $table->foreign('idFase')->references('id')->on('fase');

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
        Schema::dropIfExists('asignacionFaseProyecto');
    }
}
