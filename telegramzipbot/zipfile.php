<?php
define('BOT_TOKEN', 'SEU TOKEN DO TELEGRAM');
define('TOKEN',md5(uniqid(rand(), true)));

function zipFiles(){
	$zip = new ZipArchive();

	$DelFilePath=$_SERVER['DOCUMENT_ROOT']."/seubot/files/".TOKEN.".zip";

	if(file_exists($DelFilePath)) {
		unlink($DelFilePath); 
	}
	if ($zip->open($DelFilePath, ZIPARCHIVE::CREATE) != TRUE) {
		die("Could not open archive");
	}
	
	$path=$_SERVER['DOCUMENT_ROOT']."/seubot/files";
	$files = scandir($path);
	$files = array_diff(scandir($path), array('.', '..'));
	foreach($files as $file){
	  $zip->addFile($_SERVER['DOCUMENT_ROOT']."/seubot/files/".$file,$file);
	}
	// close and save archive
	$zip->close(); 
}

//Obtém a URL do arquivo hospedado nos servidores do telegram de acordo com o ID
function getFileURL($id){
    $result = file_get_contents("https://api.telegram.org/bot".BOT_TOKEN."/getFile?file_id=".$id);
	$response = json_decode($result,true);
	return "https://api.telegram.org/file/bot".BOT_TOKEN."/".$response["result"]["file_path"];
}

//Salva arquivo obtido a partir da URL do telegram
function saveFile($id){
	$file = getFileURL($id);
	$content = file_get_contents($file);
	$filename = explode("/", $file);
	file_put_contents($_SERVER['DOCUMENT_ROOT']."/seubot/files/".$filename[count($filename)-1], fopen($file, 'r'));
}

//Exclui os arquivos do servidor
function deleteFiles(){
    $path=$_SERVER['DOCUMENT_ROOT']."/seubot/files";
	$files = scandir($path);
	$files = array_diff(scandir($path), array('.', '..'));
	foreach($files as $file){
	  unlink($_SERVER['DOCUMENT_ROOT']."/seubot/files/".$file);
	}
}

?>