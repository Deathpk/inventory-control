<?php

namespace App\Exceptions\Category;

use App\Exceptions\AbstractException;

class FailedToListCategories extends AbstractException
{
    public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao listar as categorias disponíveis, por favor,  tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao listar as categorias dispiníveis";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}