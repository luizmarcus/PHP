<?php

define('BOT_TOKEN', 'TOKEN_TELEGRAM');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

define('DALLE_URL_API','https://api.openai.com/v1/images/generations');
define('TOTAL_IMGS', 1); //total de imagens a ser gerado
define('SIZE_IMGS', "1024x1024"); //tamanho das imagens a serem geradas

function processMessage($message) {
  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    
    $text = $message['text'];//texto recebido na mensagem

    if (strpos($text, "/start") === 0) {
		//envia a mensagem ao usuário
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Olá, '. $message['from']['first_name'].
		'! Eu sou um bot que gera uma imagem a partir de uma descrição feita por você. Por favor, descreva sua ideia EM INGLÊS.'));
    } else {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "Entendi! Sua imagem está sendo gerada..."));
      $response = requestImage(array('prompt' => $text, "n" => TOTAL_IMGS, "size" => SIZE_IMGS));
      sendMessage("sendPhoto", array('chat_id' => $chat_id, "photo" => $response, "caption" => $text));
    }
    
  } else {
    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, mas só compreendo mensagens em texto'));
  }
}

//Faz a requisição à API DALL-E e obtém a resposta
function requestImage($parameters) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode($parameters),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n".
                "Authorization: Bearer SEU_TOKEN_DALLE\r\n"
    )
);

$context  = stream_context_create( $options );
$response = file_get_contents(DALLE_URL_API, false, $context);
$json = json_decode($response, true);
return $json["data"][0]["url"];
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
  processMessage($update["message"]);
}

?>