<?php
require('zipfile.php');

define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');


function processMessage($message) {
  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    
    $text = $message['text'];//texto recebido na mensagem

    if (strpos($text, "/start") === 0) {
		//envia a mensagem ao usuário
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Olá, '. $message['from']['first_name'].'! Eu sou um bot que compacta arquivo e o disponibiliza no formato ZIP. Para começar, envie um arquivo e aguarde até eu processá-lo e disponibilizá-lo para o download'));
    } else {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, mas não compreendi sua mensagem. :('));
    }
  } else if (isset($message['photo'])) { //checa se existe imagem na mensagem
		
		$photo = $message['photo'][count($message['photo'])-1]; //obtém a imagem no tamanho original
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Já recebi seu arquivo. Já estou preparando a compactação....'));
		
		saveFile($photo["file_id"]); //armazena os arquivos no servidor
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Mais um momento...Enquanto compacto os arquivos...'));
		
		zipFiles(); //compacta os arquivos
		
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'O seu arquivo já está quase pronto...'));
		sleep(5);
		
	    //envia ao usuário o arquivo compactado com uma legenda
	    sendMessage("sendDocument", array('chat_id' => $chat_id, "document" => "https://www.seusite.com/seubot/files/".TOKEN.".zip", "caption" => "Arquivo finalizado!"));
	    sleep(5);
		
	    deleteFiles(); //deleta os arquivos do servidor
		
  } else {
    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, mas só compreendo mensagens que contenham imagens.'));
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