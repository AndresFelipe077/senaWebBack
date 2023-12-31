<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEstadoParticipantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estadoParticipantes', function (Blueprint $table) {
            $table->id();
            $table->string('detalleEstado');
            $table->timestamps();
        });

        DB::table('estadoParticipantes')->insert([
            ['detalleEstado' => 'ACTIVO'],
            ['detalleEstado' => 'PENDIENTE'],
            ['detalleEstado' => 'TRASLADO'],
            ['detalleEstado' => 'CANCELADO'],
            ['detalleEstado' => 'RETIRO VOLUNTARIO'],
            ['detalleEstado' => 'DESERTADO'],

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estadoParticipantes');
    }
}
