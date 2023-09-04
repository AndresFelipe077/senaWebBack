<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTipoParticipacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipoParticipacion', function (Blueprint $table) {
            $table->id();
            $table->string('detalleParticipacion');
            $table->timestamps();
        });

        DB::table('tipoParticipacion')->insert([
            ['detalleParticipacion' => 'VOCERO'],
            ['detalleParticipacion' => 'SUPLENTE'],
            ['detalleParticipacion' => 'LIDER'],
            ['detalleParticipacion' => 'APRENDIZ'],
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipoParticipacion');
    }
}
