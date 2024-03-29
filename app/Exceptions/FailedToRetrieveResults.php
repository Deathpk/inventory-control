<?php

namespace App\Exceptions;

use Throwable;

class FailedToRetrieveResults extends AbstractException
{
    public function __construct(string $reportedEntity, Throwable $thrownException)
    {
        $responseMessage = "Infelizmente não conseguimos achar resultados para sua busca. Por favor , tente novamente!";
        $logMessage = "Ocorreu um erro ao consumir o autocomplete da entidade {$reportedEntity}.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
