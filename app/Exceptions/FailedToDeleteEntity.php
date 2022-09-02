<?php


namespace App\Exceptions;


use App\Exceptions\AbstractException;

class FailedToDeleteEntity extends AbstractException
{
    public function __construct(string $reportedEntity, \Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao deletar um(a) {$reportedEntity} , tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao deletar um(a) {$reportedEntity}.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}