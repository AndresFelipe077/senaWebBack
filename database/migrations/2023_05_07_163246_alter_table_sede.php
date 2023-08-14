<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSede extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sede', function (Blueprint $table) {
            $table->unsignedInteger('idCiudad');
            $table->foreign('idCiudad')->references('id')->on('ciudad')->onDelete('cascade');
            $table->unsignedInteger('idCentroFormacion');
            $table->foreign('idCentroFormacion')->references('id')->on('centroFormacion')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sede', function (Blueprint $table) {
            $table->dropForeign(['idCiudad']);
            $table->dropColumn('idCiudad');
        });
    }
}
