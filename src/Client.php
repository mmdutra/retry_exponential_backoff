<?php

declare(strict_types=1);

namespace Mmdutra\RetryPolicyPhp;

class Client
{
    private int $retries;
    private int $timeout;

    public function __construct()
    {
        $this->retries = 0;
        $this->timeout = 0;
    }

    public function retry(
        int $retries
    ): self
    {
        $this->retries = $retries;

        return $this;
    }

    public function retryWithExponentialBackoff(
        int $retries,
        int $timeout
    ): self
    {
        $this->retries = $retries;
        $this->timeout = $timeout;

        return $this;
    }

    public function sendRequest(
        string $method,
        string $uri,
        array $body = []
    ): array
    {
        if ($this->retries > 0) {
            return $this->makeRequestWithRetries($method, $uri, $body);
        }

        return $this->makeRequest($method, $uri, $body);
    }

    public function makeRequest(
        string $method,
        string $uri,
        array $body
    )
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curlHandler, CURLOPT_URL, $uri);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);

        if (!empty($body)) {
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $body);
        }

        $result = curl_exec($curlHandler);
        $statusCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        curl_close($curlHandler);

        if ($statusCode === 503) {
            throw new BadResponseException();
        }

        return json_decode($result, true);
    }

    private function makeRequestWithRetries(string $method, string $uri, array $body)
    {
        for ($i = 0; $i < $this->retries; $i++) {
            try {
                $executedRetries = $i + 1;
                echo "Tentando pela {$executedRetries}º vez\n";
                return $this->makeRequest($method, $uri, $body);
            } catch (BadResponseException $exception) {
                $timeout = $this->timeout * pow(2, $i);
                if ($i != ($this->retries - 1)) {
                    usleep($timeout * 1000);
                }
            }
        }

        throw new BadResponseException();
    }
}