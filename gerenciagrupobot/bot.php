<?php

define('BOT_TOKEN', 'SEU_TOKEN');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define('DEFAULT_BLACKLIST', array("cachorro", "gato", "coelho", "galinha", "tigre", "onça", "rato"));

function processMessage($message) {
  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  $name = $message['from']['first_name'];
  
  if (isset($message['text'])) {
    
    $text = $message['text'];//texto recebido na mensagem
	$result = checkBlackList($text);
	
	if (strpos($text, "/blacklist") === 0) {
	    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "Olá! ".$name));
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "As palavras que estão na blacklist são: ".implode(", ", DEFAULT_BLACKLIST)));
	}else if ($result !== ""){
	    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "\u{00002757} Opa, ".$name.". Cuidado com o que você digita \u{00002757}"));
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "\u{00002934} ".$result."\r\nInfelizmente terei que deletar essa messagem.", 'reply_to_message_id' => $message_id));
	    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "\u{0000274c} A mensagem será apagada em 5 segundos. \u{0000274c}"));
		usleep(5000000);
		sendMessage("deleteMessage", array('chat_id' => $chat_id, 'message_id' => $message_id));
	    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "Pronto, mensagem apagada! \u{00002714}"));
	}
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

function checkBlackList($txt) {
	$response = [];
	$count=0;
	foreach (DEFAULT_BLACKLIST as $word) {
		if (stripos($txt, $word) !== false) {
			$count++;
			array_push($response, $word);
		}
	}
	
	if(!empty($response)){
		return "Encontrei ".$count. " palavra(s) dentre as que não devem ser usadas nesse grupo: ".implode(", ",$response);
	}else{
		return "";
	}
}

$update_response = file_get_contents("php://input");

$update = json_decode($update_response, true);

if (isset($update["message"])) {
  processMessage($update["message"]);
}

?>