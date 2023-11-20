<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rmi', function (Blueprint $table) {
            $table->id();
            $table->integer('horas');
            $table->date('fechaInicial');
            $table->date('fechaFinal');

            $table->unsignedInteger('idConfiguracionRap');

            $table->foreign('idConfiguracionRap')->references('id')->on('configuracionRap');
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
        Schema::dropIfExists('rmis');
    }
};
