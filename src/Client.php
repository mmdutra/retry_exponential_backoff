<?php

declare(strict_types=1);

namespace Mmdutra\RetryPolicyPhp;

class Client
{
    private int $retries;
    private int $multiplier;

    public function __construct()
    {
        $this->retries = 0;
        $this->multiplier = 0;
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
        int $multiplier
    ): self
    {
        $this->retries = $retries;
        $this->multiplier = $multiplier;

        return $this;
    }

    public function get(
       string $uri
    ): array
    {
        $timeout = 0;
        for ($i = 1; $i <= $this->retries; $i++) {
            try {
                echo "Tentando pela {$i}º vez\n";
                return $this->sendRequest($uri);
            } catch (\Exception $exception) {
                if ($this->multiplier) {
                    $timeout = $i * $this->multiplier;
                    if ($i == $this->retries) {
                        echo "Nâo deu mesmo, parcero\n";
                        return [];
                    }

                    sleep($timeout);
                }
                continue;
            }
        }

        return $this->sendRequest($uri);
    }

    public function sendRequest(
        string $uri
    )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $uri);
        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode === 503) {
            throw new \Exception();
        }

        return json_decode($result, true);
    }
}