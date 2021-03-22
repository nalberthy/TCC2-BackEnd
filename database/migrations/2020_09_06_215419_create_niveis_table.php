<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNiveisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('niveis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_recompensa')->nullable()->unsigned();
            $table->string('nome');
            $table->text('descricao');
            $table->boolean('ativo');
            $table->timestamps();
            $table->foreign('id_recompensa')->references('id')->on('recompensas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('niveis');
    }
}
