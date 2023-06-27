<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAprendicesTmpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aprendicesTmp', function (Blueprint $table) {
            $table->id();
            $table->string('TIPO_DOCUMENTO');
            $table->string('IDENTIFICACION');
            $table->string('NOMBRES');
            $table->string('APELLIDOS');
            $table->string('ESTADO');
            $table->string('FICHA');
            $table->string('PROGRAMA');
            $table->string('PROYECTOFORMATIVO');
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
        Schema::dropIfExists('aprendicesTmp');
    }
}
