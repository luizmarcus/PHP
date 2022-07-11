<?php

define('BOT_TOKEN', 'SEU TOKEN');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

function processMessage($message) {
  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    
    $text = $message['text'];//texto recebido na mensagem

    if (strpos($text, "/start") === 0) {
    
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Olá, '. $message['from']['first_name'].
		'! Eu sou um bot que serve de exemplo para o desenvolvimento de WebApp no Telegram. Clique no botão Abrir Loja para testar o WebApp.', 'reply_markup' => array('keyboard' => array(
      array(
          array('text'=>'Abrir Loja','web_app'=> array('url'=>'[Link do seu WebApp]'))
       )
    )
    )
  )
);
      
    } else {
    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "Não compreendi sua mensagem."));
  }
}else if ($message['web_app_data']) {
  sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => "Você comprou o : ".$message['web_app_data']['data']));
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
  processMessage($update["message"]);
}

?>