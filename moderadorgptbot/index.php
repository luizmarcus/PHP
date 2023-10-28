<?php

define('BOT_TOKEN', 'TELEGRAM_BOT_API_TOKEN');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

define('CHATGPT_API','https://api.openai.com/v1/moderations');

function processMessage($message) {
  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  $name = $message['from']['first_name'];
  
  if (isset($message['text'])) {
    
    $text = $message['text'];//texto recebido na mensagem
	$result = checkViolation($text);//verifica se ocorreu violação
	
	if ($result==1){
	    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "\u{00002757} Opa, ".$name.". Cuidado com o que você digita \u{00002757}"));
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "\u{00002934}\r\nInfelizmente terei que deletar essa messagem.", 'reply_to_message_id' => $message_id));
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

//Faz a requisição à ChatGpt API e obtém a resposta
function checkViolation($txt) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode(array('input' => $txt)),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n".
                "Authorization: Bearer CHATGPT_API_KEY\r\n"
    )
);

$context  = stream_context_create( $options );
$response = file_get_contents(CHATGPT_API, false, $context);
$json = json_decode($response, true);
return $json['results'][0]['flagged'];
}

$update_response = file_get_contents("php://input");

$update = json_decode($update_response, true);

if (isset($update["message"])) {
  processMessage($update["message"]);
}

?>