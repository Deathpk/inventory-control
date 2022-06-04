<?php

namespace App\Exceptions\Auth;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class TokenDoesNotExistOnCompany extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "O token não existe para a companhia logada.";
        $logMessage = "Ocorreu um erro ao buscar um token de API. O token não existe na companhia logada.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
