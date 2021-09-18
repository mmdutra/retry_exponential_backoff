<?php

declare(strict_types=1);

namespace Mmdutra\RetryPolicyPhp;

class BadResponseException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Erro no servidor!');
    }
}