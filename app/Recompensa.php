<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recompensa extends Model
{
    protected $table = 'recompensas';

    protected $fillable = ['nome', 'imagem', 'pontuacao','id_logic_live'];
}
