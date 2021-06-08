<?php

namespace App\Http\Controllers\ModuloCalculoProposicional;

use Illuminate\Http\Request;
use App\Http\Controllers\ModuloCalculoProposicional\Formula\Argumento;
use App\Http\Controllers\ModuloCalculoProposicional\Formula\Regras;
use App\Http\Controllers\Controller;
use Symfony\Component\Console\Logger\ConsoleLogger;

class Construcao extends Controller
{
    function __construct() {
        $this->arg = new Argumento;
        $this->reg = new Regras;
    }

    // Gera etapa de apresentação inicial
    public function gerar($derivacao,$premissas){
        $derivacoes=[];
        $indice=1;
        foreach ($derivacao as $i) {
            $i->setIndice($indice);
            if (in_array($i->getPremissa(), $premissas, true)){
                $i->setIdentificacao('p');
            }
            $derivacoes[]= ['indice'=>$indice,'str'=>$this->arg->stringArg($i->getPremissa()->getValor_obj()),'ident'=>$i->getIdentificacao(),'hip'=>$i->getHipotese()];
            $indice+=1;
        }

        return $derivacoes;
    }

    public function aplicarRegra($derivacoes,$linha1,$linha2,$linha3,$regra,$xml_entrada,$conclusao){
        $linha1=$linha1-1;

        if ($linha2 != null){
            $linha2=$linha2-1;
        }
        if ($linha3 != null){
            $linha3=$linha3-1;
        }


// -----------------------------------------VERIFICAÇÃO DE INDICE POR TAMANHO DA LISTA ---------------------------
        if($linha1>=count($derivacoes)){
            return False;
        }

        if($linha2 >= count($derivacoes)){

            return False;
        }
        if($linha3 >= count($derivacoes)){

            return False;
        }
// --------------------------------------------------------------------------------------------------------------


        if ($regra == 'Modus_Ponens'){
            if ($linha1 == -1){return False;}
            if ($linha2 == -1){return False;}
            if ($derivacoes[$linha1]->getPremissa()->getValor_obj()->getTipo()=="CONDICIONAL"){
                if($derivacoes[$linha1]->getPremissa()->getValor_obj()->getEsquerdaValor()==$derivacoes[$linha2]->getPremissa()->getValor_obj()->getValor()){
                    $aplicado= $this->reg->ModusPonens($derivacoes[$linha1],$derivacoes[$linha2]);
                    $aplicado->setIdentificacao(($linha1+1).','.($linha2+1).' mp');

                    //--------------------------- Verificador de Hipotese------------------------
                    // pega ultimo objeto de derivação e verifica o indice da hipotese
                    $temp_hip= $derivacoes[count($derivacoes)-1]->getHipotese();

                    // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

                    if ($temp_hip!=null){
                        $aplicado->setHipotese($temp_hip);
                    }
                    //-------------------


                    array_push($derivacoes,$aplicado);
                    return $derivacoes;

                }
            }

            elseif ($derivacoes[$linha2]->getPremissa()->getValor_obj()->getTipo()=="CONDICIONAL"){
                if($derivacoes[$linha2]->getPremissa()->getValor_obj()->getEsquerdaValor()==$derivacoes[$linha1]->getPremissa()->getValor_obj()->getValor()){
                    $aplicado=$this->reg->ModusPonens($derivacoes[$linha1],$derivacoes[$linha2]);
                    $aplicado->setIdentificacao(($linha2+1).','.($linha1+1).' mp');
                    //--------------------------- Verificador de Hipotese------------------------
                    // pega ultimo objeto de derivação e verifica o indice da hipotese
                    $temp_hip= $derivacoes[count($derivacoes)-1]->getHipotese();

                    // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

                    if ($temp_hip!=null){
                        $aplicado->setHipotese($temp_hip);
                    }
                    //-------------------
                    array_push($derivacoes,$aplicado);
                    return $derivacoes;
                }

            }
            else{
                return False;
            }

        }

        elseif($regra=='Introducao_Disjuncao'){
            if($xml_entrada == null){return FALSE;};

            try{$xml= simplexml_load_string($xml_entrada);}
            catch(\Exception $e){return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);}
            $obj_xml = $this->arg->arrayPremissas($xml);

            $aplicado=$this->reg->IntroducaoDisjuncao($derivacoes[$linha1],$obj_xml[0]);

            $aplicado->setIdentificacao(($linha1+1).' vI');

        //--------------------------- Verificador de Hipotese------------------------
            // pega ultimo objeto de derivação e verifica o indice da hipotese
            $temp_hip= $derivacoes[count($derivacoes)-1]->getHipotese();

            // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

            if ($temp_hip!=null){
                $aplicado->setHipotese($temp_hip);
            }
            //-------------------

            array_push($derivacoes,$aplicado);
            return $derivacoes;
        }
        elseif($regra=='Eliminacao_Disjuncao'){
            $aplicado=$this->reg->EliminacaoDisjuncao($derivacoes[$linha1],$derivacoes[$linha2],$derivacoes[$linha3]);
            $aplicado->setIdentificacao(($linha1+1).','.($linha2+1).','.($linha3+1).' vE');

            //--------------------------- Verificador de Hipotese------------------------
            // pega ultimo objeto de derivação e verifica o indice da hipotese
            $temp_hip= $derivacoes[count($derivacoes)-1]->getHipotese();

            // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

            if ($temp_hip!=null){
                $aplicado->setHipotese($temp_hip);
            }
            //-------------------

            array_push($derivacoes,$aplicado);
            return $derivacoes;
        }
        elseif($regra=='Introducao_Conjuncao'){
            $aplicado=$this->reg->IntroducaoConjuncao($derivacoes[$linha1],$derivacoes[$linha2]);
            $aplicado->setIdentificacao(($linha1+1).','.($linha2+1).' ^I');

            //--------------------------- Verificador de Hipotese------------------------
            // pega ultimo objeto de derivação e verifica o indice da hipotese
            $temp_hip= $derivacoes[count($derivacoes)-1]->getHipotese();

            // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

            if ($temp_hip!=null){
                $aplicado->setHipotese($temp_hip);
            }
            //-------------------
            array_push($derivacoes,$aplicado);
            return $derivacoes;
        }

        elseif($regra=='Eliminacao_Conjuncao'){
            $derivacoes= $this->reg->EliminacaoConjuncao($derivacoes,$derivacoes[$linha1],$linha1);
            return $derivacoes;
        }
        elseif($regra=='Eliminacao_Negacao'){
            $derivacoes=$this->reg->ElimicacaoNegacao($derivacoes,$derivacoes[$linha1],$linha1);
            return $derivacoes;
        }
        elseif($regra=='Introducao_Bicondicional'){
            if($derivacoes[$linha1]->getPremissa()->getValor_obj()->getTipo()=='CONDICIONAL' and $derivacoes[$linha2]->getPremissa()->getValor_obj()->getTipo()=='CONDICIONAL'){
                $aplicado=$this->reg->IntroducaoBicondicional($derivacoes[$linha1],$derivacoes[$linha2]);

                $aplicado->setIdentificacao(($linha1+1).','.($linha2+1).' ↔I');
                array_push($derivacoes,$aplicado);
                return $derivacoes;
            }
            return FALSE;

        }
        elseif($regra=='Eliminacao_Bicondicional'){
            $aplicado= $this->reg->EliminacaoBicondicional($derivacoes,$derivacoes[$linha1],$linha1);
            return $aplicado;
        }

        elseif($regra=='Hipotese_PC'){
            if($xml_entrada == null){return FALSE;};

            try{$xml= simplexml_load_string($xml_entrada);}
            catch(\Exception $e){return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);}


            $obj_xml = $this->arg->arrayPremissas($xml)[0];
            $aplicado = $this->reg->PC($obj_xml);

            $aplicado->setIdentificacao('Hip_PC');
            // pega ultimo objeto de derivação e verifica o indice da hipotese
            $temp_hip= $derivacoes[count($derivacoes)-1]->getHipotese();

           // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior

            if ($temp_hip == null){
                $aplicado->setHipotese('1');
            }
            elseif ($temp_hip!=null){
                $aplicado->setHipotese(strval(intval($temp_hip)+1));
            }
            array_push($derivacoes,$aplicado);

            return $derivacoes;
        }

        elseif($regra=='Hipotese_Raa'){
            if($xml_entrada == null){return FALSE;};

            try{$xml= simplexml_load_string($xml_entrada);}
            catch(\Exception $e){return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);}


            $obj_xml = $this->arg->arrayPremissas($xml)[0];
            $aplicado = $this->reg->RAA($conclusao[0]->getValor_obj(),$obj_xml);
            if($aplicado){
                $aplicado->setIdentificacao('Hip_Raa');

                // pega ultimo objeto de derivação e verifica o indice da hipotese
                $temp_hip= $derivacoes[count($derivacoes)-1]->getHipotese();
    
               // verifica se hipotese ja foi atribuida, se não seta valor do nivel 1, se sim realiza incremento no valor anterior
    
                if ($temp_hip == null){
                    $aplicado->setHipotese('1');
                }
                elseif ($temp_hip!=null){
                    $aplicado->setHipotese(strval(intval($temp_hip)+1));
                }
                array_push($derivacoes,$aplicado);
    
                return $derivacoes;
            }
           
       }

        elseif($regra=='Finish_Hip'){
            // verifica tipo de regra hipotetica
            if($derivacoes[$linha1]->getIdentificacao()=="Hip_PC"){
                if($derivacoes[$linha1]->getHipotese()==$derivacoes[$linha2]->getHipotese()){
                    // aplica regra de pc - basicamente cria um condicional
                    $aplicado = $this->reg->FinalizarHipPC($derivacoes[$linha1],$derivacoes[$linha2]);
                    $aplicado->setIdentificacao(($linha1+1).'-'.($linha2+1).' Hip-PC');
                    if($derivacoes[$linha1]->getHipotese()==1){
                        $aplicado->setHipotese(null);
                    }
                    else{
                        $aplicado->setHipotese(strval(intval($derivacoes[$linha1]->getHipotese())-1));
                    }
                    array_push($derivacoes,$aplicado);
                    return $derivacoes;
                }
            }

            elseif($derivacoes[$linha1]->getIdentificacao()=="Hip_Raa"){
                if($derivacoes[$linha1]->getHipotese()==$derivacoes[$linha2]->getHipotese()){
                    $aplicado = $this->reg->FinalizarHipRAA($derivacoes[$linha2], $conclusao[0]);

                    $aplicado->setIdentificacao(($linha1+1).'-'.($linha2+1).' Hip-RAA');

                    if($derivacoes[$linha1]->getHipotese()==1){
                        $aplicado->setHipotese(null);
                    }
                    else{
                        $aplicado->setHipotese(strval(intval($derivacoes[$linha1]->getHipotese())-1));
                    }
                    array_push($derivacoes,$aplicado);
                    return $derivacoes;
                }
            }



        }

    }

    #reconstrói objeto a partir de array com regras aplicadas anteriormente
    public function gerarPasso($derivacao,$passo,$conclusao){
        if($passo!=[]){
            foreach ($passo as $i) {
                $derivacao= $this->aplicarRegra($derivacao,$i['entrada1'],$i['entrada2'],$i['entrada3'],$i['regra'],$i['xml_entrada'],$conclusao);
            }
            return $derivacao;
        }
        else{
            return $derivacao;
        }

    }


    public function verificaConclusao($conclusao,$derivacao){
        // percorre lista de derivação e verifica as conclusões
        foreach ($derivacao as $i){
            if($this->arg->stringArg($conclusao[0]->getValor_obj())==$this->arg->stringArg($i->getPremissa()->getValor_obj())){
                return TRUE;
            }
        }

    }

}
