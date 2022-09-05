<?php

namespace App\Exceptions;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToUpdateEntity extends AbstractException
{
    #[Pure] public function __construct(string $reportedEntity, \Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao atualizar um(a) {$reportedEntity} , tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao atualizar um(a) {$reportedEntity}.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
