<?php

namespace App\Http\Controllers\ModuloCalculoProposicional\Formula;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloCalculoProposicional\Formula;


class Regras extends Controller
{
    function __construct() {
        $this->arg = new Argumento;
    }

    public function ModusPonens($derivacao,$premissa1,$premissa2){
        $newpremissa1 = clone $premissa1->getPremissa()->getValor_obj();
        $newpremissa2 = clone $premissa2->getPremissa()->getValor_obj();

        if($newpremissa1->getTipo()=="CONDICIONAL"){
            if($newpremissa1->getEsquerdaValor()==$newpremissa2->getValor()){
                if($newpremissa2->getNegado()==$newpremissa1->getEsquerda()->getNegado()){
                    return $this->arg->derivacao($this->arg->criarPremissa($newpremissa1->getDireita()));
                }
            }
        }
        elseif($newpremissa2->getTipo()=="CONDICIONAL"){
            if($newpremissa2->getEsquerdaValor()==$newpremissa1->getValor()){
                if($newpremissa1->getNegado()==$newpremissa2->getEsquerda()->getNegado()){
                    return $this->arg->derivacao($this->arg->criarPremissa($newpremissa2->getDireita()));
                }
            }
        }
    }

    public function IntroducaoDisjuncao($derivacao,$premissa1,$entrada_premissa){
        $newpremissa1 = clone $premissa1->getPremissa()->getValor_obj();
        
        

// em desenvolvimento


        $aplicado = $this->arg->derivacao($this->arg->criarpremissa($this->arg->criardisjuncao($newpremissa1,$newpremissa2)));
        $aplicado->setIdentificacao(($linha1+1).' vI');
        


        return $aplicado;
    }

    public function IntroducaoConjuncao($derivacao,$premissa1,$premissa2){
        $newpremissa1 = clone $premissa1->getPremissa()->getValor_obj();
        $newpremissa2 = clone $premissa2->getPremissa()->getValor_obj();
        
        return $this->arg->derivacao($this->arg->criarpremissa($this->arg->criarconjuncao($newpremissa1,$newpremissa2)));
    }

    public function EliminacaoConjuncao($derivacao,$premissa,$linha){
        if($premissa->getPremissa()->getValor_obj()->getTipo()== 'CONJUNCAO'){
            $newpremissa= clone $premissa->getPremissa()->getValor_obj();
            $newpremissa1= $this->arg->derivacao($this->arg->criarpremissa($newpremissa->getEsquerda()));
            $newpremissa1->setIdentificacao(($linha+1).' ^E');
    
            $newpremissa2=$this->arg->derivacao($this->arg->criarpremissa($newpremissa->getDireita()));
            $newpremissa2->setIdentificacao(($linha+1).' ^E');
    
            array_push($derivacao,$newpremissa1);
            array_push($derivacao,$newpremissa2);
            return $derivacao;
        }
        return FALSE;
    }

    public function ElimicacaoNegacao($derivacao,$premissa,$linha){
        $newpredicado = clone $premissa->getPremissa()->getValor_obj();
        if ($newpredicado->getNegado() >= 2){
            $newpredicado->eliminacaoNegacao();
            $newpredicado=$this->arg->derivacao($this->arg->criarpremissa($newpredicado));
            $newpredicado->setIdentificacao(($linha+1).' ~E');
            
            array_push($derivacao,$newpredicado);
    
            return $derivacao;
        }
        return FALSE;
    }

    public function IntroducaoBicondicional($derivacao,$premissa1,$premissa2){
        $newpremissa1 = clone $premissa1->getPremissa()->getValor_obj();
        $newpremissa2 = clone $premissa2->getPremissa()->getValor_obj();
  
        if($newpremissa1->getEsquerda()->getValor()==$newpremissa2->getDireita()->getValor()){
            if($newpremissa1->getEsquerda()->getNegado()==$newpremissa2->getDireita()->getNegado()){
                if($newpremissa1->getDireita()->getValor()==$newpremissa2->getEsquerda()->getValor()){
                    if($newpremissa1->getDireita()->getNegado()==$newpremissa2->getEsquerda()->getNegado()){
                        return $this->arg->derivacao($this->arg->criarpremissa($this->arg->criarbicondicional($newpremissa1->getEsquerda(),$newpremissa2->getEsquerda())));
                    }
                }
            }  
        }
        return FALSE;
    }

        
    public function EliminacaoBicondicional($derivacao,$premissa,$linha){
        if($premissa->getPremissa()->getValor_obj()->getTipo()=='BICONDICIONAL'){
            $esquerda = clone $premissa->getPremissa()->getValor_obj()->getEsquerda();
            $direita = clone $premissa->getPremissa()->getValor_obj()->getDireita();
            $newpremissa1=$this->arg->derivacao($this->arg->criarpremissa($this->arg->criarcondicional($direita,$esquerda)));
            $newpremissa2=$this->arg->derivacao($this->arg->criarpremissa($this->arg->criarcondicional($esquerda,$direita)));
    
            $newpremissa1->setIdentificacao(($linha+1).' ↔E');
            $newpremissa2->setIdentificacao(($linha+1).' ↔E');
            array_push($derivacao,$newpremissa1);
            array_push($derivacao,$newpremissa2);
            return $derivacao;
        }
        return FALSE;
    }

    public function EliminacaoDisjuncao($derivacao, $premissa1, $premissa2, $premissa3, $linhas){
        if ($premissa1->getPremissa()->getValor_obj()->getTipo()== 'DISJUNCAO'){
            if($premissa2->getPremissa()->getValor_obj()->getTipo()=='CONDICIONAL' && $premissa3->getPremissa()->getValor_obj()->getTipo()=='CONDICIONAL'){
                if ($premissa2->getPremissa()->getValor_obj()->getEsquerda() == $premissa1->getPremissa()->getValor_obj()->getEsquerda() || $premissa2->getPremissa()->getValor_obj()->getEsquerda() == $premissa1->getPremissa()->getValor_obj()->getDireita()){
                    if ($premissa3->getPremissa()->getValor_obj()->getEsquerda() == $premissa1->getPremissa()->getValor_obj()->getEsquerda() || $premissa3->getPremissa()->getValor_obj()->getEsquerda() == $premissa1->getPremissa()->getValor_obj()->getDireita()){
                        if ($premissa2->getPremissa()->getValor_obj()->getDireita()==$premissa3->getPremissa()->getValor_obj()->getDireita()){
                            $newpremissa= $this->arg->derivacao($this->arg->criarpremissa(clone $premissa2->getPremissa()->getValor_obj()->getDireita()));
                            $newpremissa->setIdentificacao(($linhas).' vE');
                            array_push($derivacao,$newpremissa);
                            return $derivacao;
                        }
                       return FALSE;
                    }
                    return FALSE;
                }
                return FALSE;
            }
            return FALSE;
        }
        return FALSE;
    }

}
