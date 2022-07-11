<?php

define('URL_API','https://api.unsplash.com/search/photos/?client_id=[CLIENT_ID]&query=computer&orientation=landscape&per_page=8');

$response = file_get_contents(URL_API);

$json = json_decode($response, true);

$images = array();
foreach($json['results'] as $image){
    array_push($images, $image['urls']['regular']);    
}

echo json_encode($images);

?>