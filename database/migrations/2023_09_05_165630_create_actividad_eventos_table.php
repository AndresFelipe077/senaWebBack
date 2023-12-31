<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividadEvento', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('idInfraestructura')->onDelete('cascade')->nullable();
            $table->foreign('idInfraestructura')->references('id')->on('infraestructura')->onDelete('cascade');

            $table->text('observacion')->nullable();

            $table->date('fechaInicial');
            
            $table->date('fechaFinal')->nullable();

            $table->unsignedInteger('idParticipante')->nullable();
            $table->foreign('idParticipante')->references('id')->on('asignacionParticipante')->onDelete('cascade');

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
        Schema::dropIfExists('actividad_eventos');
    }
}
