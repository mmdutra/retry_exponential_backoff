<?php

require_once 'vendor/autoload.php';

use Mmdutra\RetryPolicyPhp\Client;

$client = new Client();
$response = $client
    ->retry(4)
    ->withExponentialBackoff(100, 3)
    ->sendRequest('GET', 'http://localhost:8000');

var_dump($response);