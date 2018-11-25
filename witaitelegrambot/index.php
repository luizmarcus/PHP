<?php

require('parser.php');

define('BOT_TOKEN', 'SEUTOKEN);
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

function processMessage($message, $text) {
  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($text)) {
    if ($text === "mega") {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('megasena', $text)));
    } else if ($text === "quina") {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('quina', $text)));
    } else if ($text === "lotomania") {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('lotomania', $text)));
    } else if ($text === "lotofacil") {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('lotofacil', $text)));
    } else {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, mas não entendi essa mensagem. :('));
    }
  } else {
    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, mas não entendi essa mensagem. :('));
  }
}

function sendMessage($method, $parameters) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode($parameters),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);

$context  = stream_context_create( $options );
file_get_contents(API_URL.$method, false, $context );
}

$update_response = file_get_contents("php://input");

$update = json_decode($update_response, true);

if (isset($update["message"])) {
  $witai_response = callWitAi(str_replace(' ', '%20',$update["message"]['text']));
  processMessage($update["message"],$witai_response);
}

function callWitAi($query){
	$authorization = "Authorization: Bearer WITAITOKEN";
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
	$entity_loteria = $npl['loteria'];
	$entity_nome_loteria = $npl['nome_loteria'];
    
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
	}else if($entity_loteria[$number]['value'] === 'resultados'){
		$confidence = 0;
		foreach ($entity_nome_loteria as $key => $loteria){
	
			if ($loteria['confidence']>$confidence && $loteria['confidence']>0.8){
				$number = $key;
				$confidence = $loteria['confidence'];
			}	
		}
		if ($confidence == 0){
			return "";
		}else{
			return $entity_nome_loteria[$number]['value'];
		}
	}else{
		return "";
	}
}

?>