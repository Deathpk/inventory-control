<?php

namespace App\Exceptions\Auth;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToRevokeApiToken extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , Ocorreu um erro ao deletar o token. Por favor , tente novamente.";
        $logMessage = "Ocorreu um erro ao deletar um token de API.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
