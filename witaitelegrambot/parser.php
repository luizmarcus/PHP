<?php

include('simple_html_dom.php');

define('BASE_URL',"http://g1.globo.com/loterias/");
define('URL_MEGA', BASE_URL.'megasena.html');
define('URL_QUINA', BASE_URL.'quina.html');
define('URL_LOTOMANIA', BASE_URL.'lotomania.html');
define('URL_LOTOCACIL', BASE_URL.'lotofacil.html');

function getResult($lottery, $title){

	$out = "Resultado - ".$title;

	if($lottery=="megasena"){
		$out .= parser(URL_MEGA);
	}elseif($lottery=="quina"){
		$out .= parser(URL_QUINA);
	}elseif($lottery=="lotofacil"){
		$out .= parser(URL_LOTOMANIA);
	}else{
		$out .= parser(URL_LOTOCACIL);
	}

	return $out;
}

function parser($url){
	//obtém o html da página
	$html = file_get_html($url);
	if (!empty($html)) {
		$concurso_header = explode(" - ", $html->find('span.content-lottery__info',0)->plaintext);
		$concurso = $concurso_header[0];
		$data = $concurso_header[1];
		if(!isset($html->find('div[class="content-lottery__ammount desativado"]',0)->plaintext)){
			$acumulado = $html->find('div[class="content-lottery__ammount"]',0)->plaintext;
		}else{
			$acumulado = "Não Acumulou!";
		}
		$numeros = "";
		foreach ($html->find('div[class="content-lottery__result"]') as $numero) {
			$numeros .= $numero->plaintext . "  ";
		}
		$premios = "";
		foreach ($html->find('div[class="content-lottery__awards"]')[0]->find('tr') as $premio) {
			$premios .= "\n" . $premio->find('td.col-acertos',0)->plaintext." - " . $premio->find('td.col-ganhadores',0)->plaintext;
			if (strpos($premio->find('td.col-premio',0)->plaintext,"-")==false) {
				$premios .= " ganhadores "." - ".$premio->find('td.col-premio',0)->plaintext;
			}else{
			    $premios .= $premio->find('td.col-premio',0)->plaintext;
			}
		}
		return "\n---------------".
		"\n".$concurso .
		"\nDATA: " . $data .
		"\nNÚMEROS:  " . $numeros .
		"\n".$acumulado.
		"\n---------------".
		"\nPREMIAÇÕES" . $premios;
	}else{
		return "\nNão encontrado";
	}
}

?>