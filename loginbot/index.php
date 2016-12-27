<?php
//https://developers.facebook.com/docs/messenger-platform/account-linking

define('BOT_TOKEN', 'SEU ACCESS TOKEN');
define('VERIFY_TOKEN', 'SEU VERIFY TOKEN');
define('API_URL', 'https://graph.facebook.com/v2.6/me/messages?access_token='.BOT_TOKEN);

$hub_verify_token = null;

function processMessage($message) {
  // processa a mensagem recebida
  
  $sender = $message['sender']['id'];
  $text = $message['message']['text'];//texto recebido na mensagem
  $account_linking_status = $message['account_linking']['status'];
  
  if (isset($text)) {
		if ($text === "login" || $text === "Login") {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("attachment" => array('type'=>'template','payload'=>array('template_type'=>'generic','elements'=>array(array("title"=> "Teste de Login","image_url"=>"https://luizmarcus.com/wp-content/uploads/2016/08/new-app-768x428.png",'buttons'=>array(array('type'=>'account_link','url'=>'LINK PARA O SEU SISTEMA')))))))));
		}else {
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => "Olá! Eu sou um bot que serve com exemplo da funcionalidade 'Vinculação de Contas' da API do Facebook Messenger. Digite 'login' para começar.")));
		  sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => "Quer ver o código-fonte desse bot? Acesse: https://luizmarcus.com/php/vinculacao-de-conta-account-linking-no-facebook-messenger/")));
		}
  } else if(isset($account_linking_status)){
      if($account_linking_status==="linked"){
          sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => "Login feito com sucesso!")));
      }else{
          sendMessage(array('recipient' => array('id' => $sender), 'message' => array("text" => "Logout feito com sucesso")));
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