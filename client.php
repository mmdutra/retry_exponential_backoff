<?php

require_once 'vendor/autoload.php';

use Mmdutra\RetryPolicyPhp\Client;

$client = new Client();

echo "EXPONENTIAL BACKOFF\n";
$response = $client
    ->retry(5)
    ->withExponentialBackoff(100)
    ->sendRequest('GET', 'http://localhost:8000');

echo "EXPONENTIAL BACKOFF WITH JITTER\n";
$response = $client
    ->retry(5)
    ->withExponentialBackoff(100)
    ->withJitter()
    ->sendRequest('GET', 'http://localhost:8000');

var_dump($response);