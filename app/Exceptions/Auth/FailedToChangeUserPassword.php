<?php

namespace App\Exceptions\Auth;

use App\Exceptions\AbstractException;

class FailedToChangeUserPassword extends AbstractException
{
    public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! \n Ocorreu um erro inesperado ao tentar trocar sua senha, tente novamente. Se o erro persistir, por favor, contacte o suporte.";
        $logMessage = "Ocorreu um erro ao trocar a senha de um usuário.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException, statusCode:500);
    }
}
