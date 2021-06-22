<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloCalculoProposicional\Formula\Argumento;
use App\Http\Controllers\ModuloCalculoProposicional\Construcao;
use App\Http\Controllers\ModuloCalculoProposicional\Formula\Regras;


class CalculoProposicionalController extends Controller
{
    function __construct() {
        $this->arg = new Argumento;
        $this->constr = new Construcao;
        $this->regra= new Regras;

    }


    public function Derivacao(Request $request){
        try{
            $xml = simplexml_load_string($request->xml);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);
        }


        #Gera arrays
        $premissas = $this->arg->arrayPremissas($xml);
        $conclusao = $this->arg->arrayConclusao($xml);
        $derivacao =  $this->arg->arrayDerivacao($premissas);

        #etapas usuario

        $listaDerivacoes = '{}';

        #gera formula para exibição
        $formula=$this->arg->formula($premissas,$conclusao);
        # array de objeto derivações
        $derivacoes=$this->constr->gerar($derivacao,$premissas);


        return response()->json(['derivacoes'=>$derivacoes, 'formula'=>$formula, 'listaDerivacoes'=>$listaDerivacoes, 'msg'=>'' ]);
        // return view('derivacao',['derivacoes'=>$derivacoes,'listaFormulas'=> $listaFormulas, 'formula'=>$formula, 'idXml'=>$id, 'listaDerivacoes'=>$listaDerivacoes, 'msg'=>'' ]);

    }


    public function Derivar(Request $request){
        $formulario = $request->all();
        #ler a string xml, e a transforma em objeto
        try{$xml = simplexml_load_string($request->xml);}
        catch(\Exception $e){return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);}
        #-----



        #--------------Mensagem----------------------
        $msg='';

        #arrays
        $premissas = $this->arg->arrayPremissas($xml);
        $conclusao = $this->arg->arrayConclusao($xml);
        $derivacao =  $this->arg->arrayDerivacao($premissas);

        #gera lista de obj etapa inicial


        #-------transforma o json em array----
        $listaDerivacoes=$formulario['derivacoes'];
        $listaDerivacoes=json_decode($listaDerivacoes,true);
        #-------------------------------------

        #-------transforma o json em array----


        #-------- Reconstrói primeira etapa de derivação visual---------
        $derivacoes=$this->constr->gerar($derivacao,$premissas);
        #--------------------------------------------------------
        #-------- Reconstró passo a passo -----------------------
        $derivacaoPasso= $this->constr->gerarPasso($derivacao,$listaDerivacoes,$conclusao);
        #--------------------------------------------------------





        #Deriva a tentativa atual, caso der erro, retorna valor boleano
        if($formulario['entrada1'] or $formulario['regra']=="Hipotese_PC" or $formulario['regra']=="Hipotese_Raa"){

            $derivacaofinal=$this->constr->aplicarRegra($derivacaoPasso,$formulario['entrada1'],$formulario['entrada2'],$formulario['entrada3'],$formulario['regra'],$formulario['xml_entrada'], $conclusao);

        }
 
        if ($derivacaofinal != FALSE){
            if($this->constr->verificaConclusao($conclusao,$derivacaofinal)==TRUE){
                $msg='Atingiu a Conclusão';
            }
        }


        if($derivacaofinal==False){
            $msg='Erro ao Tentar Derivar';
            $derivacoes=$this->constr->gerar($derivacaoPasso,$premissas);
            $formula=$this->arg->formula($premissas,$conclusao);
            $listaDerivacoes =json_encode ($listaDerivacoes);
        }
        else{
            $derivacoes=$this->constr->gerar($derivacaofinal,$premissas);
            $formula=$this->arg->formula($premissas,$conclusao);

            array_push( $listaDerivacoes, ['entrada1'=>$formulario['entrada1'],'entrada2'=>$formulario['entrada2'],'entrada3'=>$formulario['entrada3'],'regra'=>$formulario['regra'],'xml_entrada'=>$formulario['xml_entrada']]);
            $listaDerivacoes =json_encode ($listaDerivacoes);

        }

        return response()->json(['derivacoes'=>$derivacoes, 'formula'=>$formula, 'listaDerivacoes'=> $listaDerivacoes,'msg'=>$msg]);

    }

}


