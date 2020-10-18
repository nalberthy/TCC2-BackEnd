<?php

namespace App\Http\Controllers\ModuloCalculoProposicional\Models\Formula;

use Illuminate\Database\Eloquent\Model;

class Predicado
{
    protected $valor;
    protected $negado;
    protected $tipo;
    protected $esquerda; 
    protected $direita;


    function __construct($valor,$negado,$tipo,$esquerda,$direita) {
       $this->valor=$valor;
       $this->negado=$negado;
       $this->tipo=$tipo;
       $this->direita=$direita;
       $this->esquerda=$esquerda;
  
  
   }

 
    public function getValor(){
        return $this->valor;
    }

    public function setValor($valor){
        $this->valor=$valor;

    }

    public function getTipo(){
        return $this->tipo;
    }

    public function setTipo($tipo){
        $this->tipo=$tipo;
    }

    public function getDireita(){
        return $this->direita;
    }

    public function setDireita($direita){
        $this->direita=$direita;
    }

    public function getEsquerda(){
        return $this->esquerda;
    }

    public function setEsquerda($esquerda){
        $this->esquerda=$esquerda;
    }

    public function getNegado(){
        return $this->negado;
    }

    public function setNegado($negado){
        $this->negado=$negado;
    }

    public function addNegacao($negado){
        $this->negado= $this->negado+1;
    }

    public function getTipoPredicado(){
        return $this->tipo;
    }

    public function removeNegacao(){
        $this->negado= $this->negado-1;
    }
    public function eliminacaoNegacao(){
        $this->negado= $this->negado-2;
    }

    public function existeEsqDis (){
        if ($this->esquerda==null && $this->direita==null){
            return true;
        }
        return false;
    }
    public function getEsquerdaValor(){
        return $this->esquerda->valor;
    }
    public function getDireitaValor(){
        return $this->direita->valor;
    }
}
