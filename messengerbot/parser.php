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
		$out .= parser(URL_LOTOCACIL);
	}else{
		$out .= parser(URL_LOTOMANIA);	
	}

	return $out;
}

function parser($url){

	//obtém o html da página
	$html = file_get_html($url);

	if (!empty($html)) {

		$concurso = $html->find('span.numero-concurso',0)->plaintext;
		$data = $html->find('span.data-concurso',0)->plaintext;
		if(!isset($html->find('div[class="dados-nao-acumulou desativado"]',0)->plaintext)){
			$acumulado = $html->find('span.label-valor-acumulado',0)->plaintext." ".$html->find('span.valor-acumulado',0)->plaintext;
		}else{
			$acumulado = "Não Acumulou!";
		}


		$numeros = "";
		foreach ($html->find('span.numero-sorteado') as $numero) {
			$numeros .= $numero->plaintext . "  ";
		}

		$premios = "";
		foreach ($html->find('tr.premio') as $premio) {
			$premios .= "\n" . $premio->find('td.label-premio',0)->plaintext . " - " . $premio->find('td.ganhadores-premio',0)->plaintext;
			if ($premio->find('td.rateio-premio',0)->plaintext != "-") {
				$premios .= " - ".$premio->find('td.rateio-premio',0)->plaintext;
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