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
            $table->string('TIPO_DOCUMENTO')->nullable();
            $table->string('IDENTIFICACION')->nullable();
            $table->string('NOMBRES')->nullable();
            $table->string('APELLIDOS')->nullable();
            $table->string('ESTADO')->nullable();
            $table->string('FICHA')->nullable();
            $table->string('PROGRAMA')->nullable();
            $table->string('PROYECTOFORMATIVO')->nullable();
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
