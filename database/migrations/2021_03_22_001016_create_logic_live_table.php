<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogicLiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logic_live', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->integer('meu_id');
            $table->text('hash')->nullable();
            $table->text('link')->nullable();
            $table->integer('recompensa_id')->nullable();
            $table->integer('game_id')->nullable();
            $table->integer('modulo_id')->nullable();
            $table->integer('nivel_id')->nullable();
            $table->text('nome')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativo');

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
        Schema::dropIfExists('logic_live');
    }
}
