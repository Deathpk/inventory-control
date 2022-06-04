<?php

namespace App\Exceptions\Auth;

use App\Exceptions\AbstractException;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\Pure;

class FailedToIssueNewApiToken extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! \n Ocorreu um erro ao criar uma nova chave de API, por favor , tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao criar uma chave de API para uma empresa específica.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
