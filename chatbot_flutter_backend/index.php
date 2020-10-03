<?php
require('parser.php');

function processMessage($text) {
  if (isset($text)) {
      if ($text === "mega") {
      echo getResult('megasena', $text);
    } else if ($text === "quina") {
      echo getResult('quina', $text);
    } else if ($text === "lotomania") {
      echo getResult('lotomania', $text);
    } else if ($text === "lotofacil") {
      echo getResult('lotofacil', $text);
    } else {
      echo 'Desculpe, mas não entendi a sua solicitação. =( ';
    }
  } else {
    echo 'Desculpe, mas não entendi essa mensagem. =( ';
  }
}

if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "/start") {
      echo "Olá! Em que posso te ajudar?";
    } else{
      $witai_response = callWitAi(str_replace(" ", "%20", $_GET["msg"]));
      processMessage($witai_response);
    }
}

function callWitAi($query){
	$authorization = "Authorization: Bearer SEU_TOKEN";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,'https://api.wit.ai/message?q='.$query);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response,true);
    return checkConfidence($result['entities']);
}

/*
Checa o tributo confidence da mensagem recebida. Para isso é usado o limiar de 0.8.
Um foreach é usado para cada entidade para ler todos os possíveis valores identificados pelo Wit.ai e 
verificar qual teve a maior taxa de correnpondência.
*/ 
function checkConfidence($npl) {
	if(isset($npl['nome_loteria:nome_loteria'])){
	$entity_loteria = $npl['nome_loteria:nome_loteria'];
	
	$number = 0;
	$confidence = 0;
	foreach ($entity_loteria as $key => $loteria){
		if ($loteria['confidence']>$confidence && $loteria['confidence']>0.8){
			$number = $key;
			$confidence = $loteria['confidence'];
		}	
	}
	if ($confidence == 0){
		return "";
	}else {
	    return $entity_loteria[$number]['value'];
	}
	}else{
		return "";
	}
}

?>