<?php
require('parser.php');
define('BOT_TOKEN', 'SEU_TOKEN');
define('VERIFY_TOKEN', 'SEU_VERIFY_TOKEN');
define('API_URL', 'https://graph.facebook.com/v3.0/me/messages?access_token='.BOT_TOKEN);

$hub_verify_token = null;

function processMessage($message) {
	
  $sender = $message['sender']['id'];
  $text = checkConfidence($message['message']['nlp']['entities']);
  
  if ($text === "") {
		sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => 'Não compreendi a sua mensagem! =(')));
  }else{
	if ($text === "mega") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('megasena', $text))));
		} else if ($text === "quina") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('quina', $text))));
		} else if ($text === "lotomania") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('lotomania', $text))));
		} else if ($text === "lotofacil") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('lotofacil', $text))));
		} else {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => 'Não compreendi a sua mensagem! =(')));
		}
  } 
}

function sendMessage($parameters) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode($parameters),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
$context  = stream_context_create( $options );
file_get_contents(API_URL, false, $context );
}

/*
Checa o tributo confidence da mensagem recebida. Para isso é usado o limiar de 0.8.
Um foreach é usado para cada entidade para ler todos os possíveis valores identificados pelo Wit.ai e 
verificar qual teve o maior confidence.
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

//-----VEFICA O WEBHOOK-----//
if(isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
if ($hub_verify_token === VERIFY_TOKEN) {
    echo $challenge;
}
//-----FIM VERIFICAÇÃO-----//

$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);

if (isset($update['entry'][0]['messaging'][0])) {
  processMessage($update['entry'][0]['messaging'][0]);
}


?>