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

    public function ModusPonens($premissa1,$premissa2){
        $newpremissa1 = clone $premissa1->getPremissa()->getValor_obj();
        $newpremissa2 = clone $premissa2->getPremissa()->getValor_obj();

        if($newpremissa1->getTipo()=="CONDICIONAL"){
            // pega valor da esquerda e compara com valor da segunda premissa
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

    public function IntroducaoDisjuncao($premissa1, $xml_entrada){

        $newpremissa1 = clone $premissa1->getPremissa()->getValor_obj();

        $aplicado = $this->arg->derivacao($this->arg->criarpremissa($this->arg->criardisjuncao($newpremissa1, $xml_entrada->getValor_obj())));

        return $aplicado;
    }

    public function IntroducaoConjuncao($premissa1,$premissa2){
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

            //--------------------------- Verificador de Hipotese------------------------
            // pega ultimo objeto de derivação e verifica o indice da hipotese
            $temp_hip= $derivacao[count($derivacao)-1]->getHipotese();

            // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

            if ($temp_hip!=null){
                $newpremissa1->setHipotese($temp_hip);
                $newpremissa2->setHipotese($temp_hip);
            }
            //-------------------
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

            //--------------------------- Verificador de Hipotese------------------------
            // pega ultimo objeto de derivação e verifica o indice da hipotese
            $temp_hip= $derivacao[count($derivacao)-1]->getHipotese();

            // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

            if ($temp_hip!=null){
                $newpredicado->setHipotese($temp_hip);
            }
            //-------------------
            array_push($derivacao,$newpredicado);

            return $derivacao;
        }
        return FALSE;
    }

    public function IntroducaoBicondicional($premissa1,$premissa2){
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

            //--------------------------- Verificador de Hipotese------------------------
            // pega ultimo objeto de derivação e verifica o indice da hipotese
            $temp_hip= $derivacao[count($derivacao)-1]->getHipotese();

            // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

            if ($temp_hip!=null){
                $newpremissa1->setHipotese($temp_hip);
                $newpremissa2->setHipotese($temp_hip);
            }
            //-------------------

            array_push($derivacao,$newpremissa1);
            array_push($derivacao,$newpremissa2);
            return $derivacao;
        }
        return FALSE;
    }

    public function EliminacaoDisjuncao($premissa1, $premissa2, $premissa3){
        // verifica se é do tipo conjunção
        if ($premissa1->getPremissa()->getValor_obj()->getTipo()== 'DISJUNCAO'){
            // verifica se os valores passados são do tipo condicional
            if($premissa2->getPremissa()->getValor_obj()->getTipo()=='CONDICIONAL' && $premissa3->getPremissa()->getValor_obj()->getTipo()=='CONDICIONAL'){
                // faz comparativo entre valor antecedente do condicional verificando se esta contido na disjuncao
                if ($premissa2->getPremissa()->getValor_obj()->getEsquerda() == $premissa1->getPremissa()->getValor_obj()->getEsquerda() || $premissa2->getPremissa()->getValor_obj()->getEsquerda() == $premissa1->getPremissa()->getValor_obj()->getDireita()){
                    // faz comparativo entre valor antecedente do condicional verificando se esta contido na disjuncao
                    if ($premissa3->getPremissa()->getValor_obj()->getEsquerda() == $premissa1->getPremissa()->getValor_obj()->getEsquerda() || $premissa3->getPremissa()->getValor_obj()->getEsquerda() == $premissa1->getPremissa()->getValor_obj()->getDireita()){
                        // verifica se o valor do consequente dos dois condicionais passados são iguais
                        if ($premissa2->getPremissa()->getValor_obj()->getDireita()==$premissa3->getPremissa()->getValor_obj()->getDireita()){
                            $newpremissa= $this->arg->derivacao($this->arg->criarpremissa(clone $premissa2->getPremissa()->getValor_obj()->getDireita()));
                            return $newpremissa;
                        }
                       return FALSE;
                    }
                    return FALSE;
                }
                return FALSE;
            }
            return FALSE;
        }
            // verifica se os valores passados são do tipo condicional
        if($premissa1->getPremissa()->getValor_obj()->getTipo()=='CONDICIONAL' && $premissa2->getPremissa()->getValor_obj()->getTipo()=='CONDICIONAL'){
            // faz comparativo entre valor antecedente do condicional verificando se esta contido na disjuncao
            if ($premissa1->getPremissa()->getValor_obj()->getEsquerda() == $premissa3->getPremissa()->getValor_obj()->getEsquerda() || $premissa1->getPremissa()->getValor_obj()->getEsquerda() == $premissa3->getPremissa()->getValor_obj()->getDireita()){
                // faz comparativo entre valor antecedente do condicional verificando se esta contido na disjuncao
                if ($premissa2->getPremissa()->getValor_obj()->getEsquerda() == $premissa3->getPremissa()->getValor_obj()->getEsquerda() || $premissa2->getPremissa()->getValor_obj()->getEsquerda() == $premissa3->getPremissa()->getValor_obj()->getDireita()){
                    // verifica se o valor do consequente dos dois condicionais passados são iguais
                    if ($premissa1->getPremissa()->getValor_obj()->getDireita()==$premissa2->getPremissa()->getValor_obj()->getDireita()){
                        $newpremissa= $this->arg->derivacao($this->arg->criarpremissa(clone $premissa1->getPremissa()->getValor_obj()->getDireita()));
                        return $newpremissa;
                    }
                    return FALSE;
                }
                return FALSE;
            }
            return FALSE;
        }
        return FALSE;
        
 
    }

    public function PC($xml_entrada){

        return $this->arg->derivacao($xml_entrada);

    }

    public function RAA($conclusao, $xml_entrada){
        $new_conclusao = clone $conclusao;
        if($new_conclusao->getNegado()!=0){
            $new_conclusao->setNegado($new_conclusao->getNegado()+1);
        }
        else{$new_conclusao->setNegado(1);}

        $new_conclusao1 = $this->arg->criarpremissa($new_conclusao);

        if($new_conclusao1->getValor_str() == $xml_entrada->getValor_str()){
            if($new_conclusao1->getValor_obj()->getNegado()==$xml_entrada->getValor_obj()->getNegado() ){
                return $this->arg->derivacao($xml_entrada);
            }
            // negações diferentes
            return FALSE;
        }
        // valor de string diferentes
        return FALSE;

    }


    public function FinalizarHipPC($premissa1, $premissa2){
        $newpremissa1 = clone $premissa1->getPremissa()->getValor_obj();
        $newpremissa2 = clone $premissa2->getPremissa()->getValor_obj();
        return $this->arg->derivacao($this->arg->criarpremissa($this->arg->criarcondicional($newpremissa1,$newpremissa2)));
    }

    public function FinalizarHipRAA($contradicao,$conclusao){
        if($contradicao->getPremissa()->getValor_obj()->getTipo()== 'CONJUNCAO'){
            $newpremissa= clone $contradicao->getPremissa()->getValor_obj();

            $newpremissa1= $this->arg->derivacao($this->arg->criarpremissa($newpremissa->getEsquerda()));
            $newpremissa2=$this->arg->derivacao($this->arg->criarpremissa($newpremissa->getDireita()));

            $new_conclusao = clone $conclusao;

            if($newpremissa1->getPremissa()->getValor_str()==$newpremissa2->getPremissa()->getValor_str()){
                // print_r($newpremissa1->getPremissa()->getValor_Obj()->getNegado());
                if(($newpremissa1->getPremissa()->getValor_Obj()->getNegado()-$newpremissa2->getPremissa()->getValor_Obj()->getNegado())==1 || ($newpremissa1->getPremissa()->getValor_Obj()->getNegado()-$newpremissa2->getPremissa()->getValor_Obj()->getNegado())==-1 ){
                    return $this->arg->derivacao($new_conclusao);
                }

                return false;
            };
            return false;
        }
    return false;


    }

}
