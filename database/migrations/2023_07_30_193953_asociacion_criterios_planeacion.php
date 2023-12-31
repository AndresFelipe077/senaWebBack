<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AsociacionCriteriosPlaneacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asociacionCriteriosPlaneacion', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_planeacion');
             $table->unsignedBigInteger('id_criterioEvaluacion')->nullable();
       

            $table->foreign('id_planeacion')->references('id')->on('planeacion');
            $table->foreign('id_criterioEvaluacion')->references('id')->on('criteriosEvaluacion');
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
        Schema::dropIfExists('asociacionCriteriosPlaneacion');
    }
}
