<?php

require_once 'vendor/autoload.php';

use Mmdutra\RetryPolicyPhp\Client;

$client = new Client();
$response = $client
//    ->retry(2)
    ->retryWithExponentialBackoff(3,1000)
    ->sendRequest('GET', 'http://localhost:8000'    );

var_dump($response);