<?php

namespace App\Exceptions\Employee;

use App\Exceptions\AbstractException;

class FailedToListEmployees extends AbstractException
{
    public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao listar os colaboradores, por favor,  tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao listar os employees de uma empresa.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
