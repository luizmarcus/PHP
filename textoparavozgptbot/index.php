<?php

define('BOT_TOKEN', 'TOKEN_DO_TELEGRAM');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

define('CHATGPT_API','https://api.openai.com/v1/audio/speech');

function processMessage($message) {
  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  $name = $message['from']['first_name'];
  
  if (isset($message['text']) && $message['text'] != '/start') {
    
    $text = $message['text'];//texto recebido na mensagem
    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "\u{00002757} Opa, ".$name."! Já estou trabalhando na conversão...."));
        
	$result = textToVoice($text);
	sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "Ufa! Terminei!"));
    
	if ($result!=null){
	    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "\u{00002757} Pronto, ".$name.". Seu texto já foi transformado em voz"));
        sendMessage("sendAudio", array('chat_id' => $chat_id, "audio" => 'https://SUA_URL_AQUI.COM/textoparavozbot/'.$result, "caption" => $text));
	    unlink($result); // exclui o arquivo de audio
	}else{
	    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "Não foi possivel realizar a conversão"));
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
function textToVoice($txt) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode(array('input' => $txt, 'model' => 'tts-1', 'voice' => 'alloy')),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n".
                "Authorization: Bearer API_KEY_OPEN_AI\r\n"
    )
);

$context  = stream_context_create( $options );
$response = file_get_contents(CHATGPT_API, false, $context);
$filename = uniqid().'.mp3'; //nomeia o arquivo recebido
file_put_contents($filename, $response); //salva o arquivo recebido
return $filename;
}

$update_response = file_get_contents("php://input");

$update = json_decode($update_response, true);

if (isset($update["message"])) {
  processMessage($update["message"]);
}

?>