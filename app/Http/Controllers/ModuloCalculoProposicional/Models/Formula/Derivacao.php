<?php

namespace App\Http\Controllers\ModuloCalculoProposicional\Models\Formula;

use Illuminate\Database\Eloquent\Model;

class Derivacao 
{
    protected $indice;
    protected $premissa;
    protected $identificacao;

    function __construct($indice,$premissa,$identificacao) {
        $this->indice=$indice;
        $this->premissa=$premissa;
        $this->identificacao=$identificacao;
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
}
