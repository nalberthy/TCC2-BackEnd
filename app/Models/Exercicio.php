<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercicio extends Model
{
    protected $table = 'exercicios';
    protected $fillable = ['nome', 'ativo', 'descricao','enunciado'];

    public function resposta()
    {
        return $this->belongsTo('App\Models\Resposta');
    }
}
