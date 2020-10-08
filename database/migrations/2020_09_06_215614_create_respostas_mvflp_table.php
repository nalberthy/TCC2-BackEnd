<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRespostasMvflpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respostas_mvflp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_exercicio');
            $table->unsignedBigInteger('id_jogador');
            $table->dateTime('tempo');
            $table->boolean('ativa');
            $table->integer('tentativas_invalidas');
            $table->timestamps();
            $table->foreign('id_exercicio')->references('id')->on('exercicios_mvflp');
            $table->foreign('id_jogador')->references('id')->on('jogadores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('respostas_mvflp');
    }
}
