<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToUpdateProduct extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao atualizar um produto , tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao atualizar um produto.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
