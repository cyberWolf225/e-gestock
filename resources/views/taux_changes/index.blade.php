<?php
require_once '../../vendor/autoload.php';
// require_once '../../../vendor/autoload.php';
use GuzzleHttp\Client;
 
try {
    $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'http://api.exchangeratesapi.io/v1/',
    ]);
      
    // get all rates
    $response = $client->request('GET', 'latest', [
        'query' => [
            'base' => 'EUR',
            'symbols' => 'EUR',
        ]
    ]);
      
    if($response->getStatusCode() == 200) {
        $body = $response->getBody();
        $arr_body = json_decode($body);
       echo "<pre>"; print_r($arr_body); echo "</pre>" ;
    }
} catch(Exception $e) {
    echo $e->getMessage();
}