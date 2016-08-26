<?php
//https://developers.facebook.com/docs/messenger-platform/webhook-reference/message-received
require('parser.php');

define('BOT_TOKEN', 'SEU ACCESS TOKEN');
define('VERIFY_TOKEN', 'SEU VERIFY TOKEN');
define('API_URL', 'https://graph.facebook.com/v2.6/me/messages?access_token='.BOT_TOKEN);

$hub_verify_token = null;

function processMessage($message) {
  // processa a mensagem recebida
  
  $sender = $message['sender']['id'];
  $text = $message['message']['text'];//texto recebido na mensagem
  
  if (isset($text)) {
		if ($text === "Mega-Sena") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('megasena', $text))));
		} else if ($text === "Quina") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('quina', $text))));
		} else if ($text === "Lotomania") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('lotomania', $text))));
		} else if ($text === "Lotofácil" || $text === "Lotofacil") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => getResult('lotofacil', $text))));
		} else {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Olá! Eu sou um bot que informa os resultados das loterias da Caixa. Será que você ganhou dessa vez? Para começar, digite o nome do jogo para o qual deseja ver o resultado')));
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