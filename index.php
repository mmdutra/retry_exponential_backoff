<?php

require_once 'vendor/autoload.php';

use Mmdutra\RetryPolicyPhp\Client;

$client = new Client();
$response = $client
//    ->retry(2)
    ->retryWithExponentialBackoff(3, 2)
    ->get('http://localhost:8000');

var_dump($response);