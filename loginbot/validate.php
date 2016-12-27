<?php
    $redirect_uri = $_GET['redirect_uri'];
    $account_linking_token = $_GET['account_linking_token'];
    $authorization_code = $_GET['authorization_code'];
    $username = $_GET['username'];
	$password = $_GET['password'];
	
	$url ="";
	if ($username === "teste" && $password === "teste"){
		$url = $redirect_uri.'&authorization_code='.$authorization_code;
	} else{
		$url = $redirect_uri;
	}
	
	header( "Location: $url" );
    
?>