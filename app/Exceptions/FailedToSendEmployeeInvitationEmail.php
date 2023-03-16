<?php

namespace App\Exceptions;
use Throwable;

class FailedToSendEmployeeInvitationEmail extends AbstractException
{
    public function __construct(Throwable $thrownException = null)
    {
        $logMessage = "Ocorreu um erro inesperado ao enviar um convite para um funcionário.";
        parent::__construct(logMessage: $logMessage, thrownException: $thrownException);
    }
}
