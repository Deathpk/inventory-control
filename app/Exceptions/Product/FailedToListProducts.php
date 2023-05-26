<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;

class FailedToListProducts extends AbstractException
{
    public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao listar os produtos disponíveis, por favor,  tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao listar os produtos dispiníveis";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
