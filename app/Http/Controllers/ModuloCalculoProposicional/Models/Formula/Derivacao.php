<?php

namespace App\Http\Controllers\ModuloCalculoProposicional\Models\Formula;

use Illuminate\Database\Eloquent\Model;

class Derivacao
{
    protected $indice;
    protected $premissa;
    protected $identificacao;
    protected $hipotese;


    function __construct($indice,$premissa,$identificacao, $hipotese) {
        $this->indice=$indice;
        $this->premissa=$premissa;
        $this->identificacao=$identificacao;
        $this->hipotese=$hipotese;
    }

    public function getIndice(){
        return $this->indice;
    }

    public function setIndice($indice){
        $this->indice=$indice;

    }

    public function getPremissa(){
        return $this->premissa;
    }

    public function setPremissa($premissa){
        $this->premissa=$premissa;
    }

    public function getIdentificacao(){
        return $this->identificacao;
    }

    public function setIdentificacao($identificacao){
        $this->identificacao=$identificacao;
    }

    public function getHipotese(){
        return $this->hipotese;
    }

    public function setHipotese($hipotese){
        $this->hipotese=$hipotese;
    }

}
