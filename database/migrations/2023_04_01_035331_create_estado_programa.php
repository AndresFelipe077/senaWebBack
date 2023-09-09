<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEstadoPrograma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estadoPrograma', function (Blueprint $table) {

            $table->increments('id');
            $table->string('estado');
            $table->timestamps();
        });

        DB::table('estadoPrograma')->insert([
            ['estado' => 'ACTIVO'],
        ]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estadoPrograma');
    }
}
