<?php

namespace App\Exceptions\Brand;

use App\Exceptions\AbstractException;

class FailedToListBrands extends AbstractException
{
    public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao listar as marcas disponíveis, por favor,  tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao listar as marcas dispiníveis";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}