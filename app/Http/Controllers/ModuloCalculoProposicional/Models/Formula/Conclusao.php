<?php

namespace App\Http\Controllers\ModuloCalculoProposicional\Models\Formula;

use Illuminate\Database\Eloquent\Model;

class Conclusao
{
    protected $valor_str;
    protected $valor_obj;
    protected $simbolo;

    function __construct($valor_str,$simbolo,$valor_obj) {
       $this->valor_str=$valor_str;
       $this->simbolo=$simbolo;
       $this->valor_obj=$valor_obj;
   }

    public function getValor_str(){
        return $this->valor_str;
    }

    public function setValor_str($valor_str){
        $this->valor_str=$valor_str;
    }

    public function getValor_obj(){
        return $this->valor_obj;
    }

    public function setValor_obj($valor_obj){
        $this->valor_obj=$valor_obj;
    }

    public function getSimbolo(){
        return $this->simbolo;
    }
}
