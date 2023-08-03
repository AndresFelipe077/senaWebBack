<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividadProyecto', function (Blueprint $table) {
            $table->increments('id');
            $table->text('nombreActividadProyecto');

            $table->unsignedInteger('idFaseProyecto');
            $table->foreign('idFaseProyecto')->references('id')->on('asignacionFaseProyecto');

            $table->text('codigoAP');
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
        Schema::dropIfExists('actividadProyecto');
    }
}
