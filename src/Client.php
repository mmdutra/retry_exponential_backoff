<?php

declare(strict_types=1);

namespace Mmdutra\RetryPolicyPhp;

class Client
{
    private int $retries = 0;
    private int $timeout = 0;
    private int $multiplier = 2;

    public function retry(
        int $retries
    ): self
    {
        $this->retries = $retries;

        return $this;
    }

    public function withExponentialBackoff(
        int $timeout,
        int $multiplier = 2
    ): self
    {
        $this->timeout = $timeout;
        $this->multiplier = $multiplier;

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
                if ($i != ($this->retries - 1)) {
                    $timeout = $this->timeout * pow($this->multiplier, $i);
                    echo "Timeout: {$timeout}ms\n";
                    usleep($timeout * 1000);
                }
            }
        }

        throw new BadResponseException();
    }
}