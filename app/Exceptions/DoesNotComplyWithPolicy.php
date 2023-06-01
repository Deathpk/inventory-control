<?php

namespace App\Exceptions;

class DoesNotComplyWithPolicy extends AbstractException
{
    public function __construct(string $reason)
    {
        $responseMessage = "Essa ação não é permitida. Motivo: {$reason}";
        parent::__construct(responseMessage: $responseMessage, statusCode:401);
    }
}