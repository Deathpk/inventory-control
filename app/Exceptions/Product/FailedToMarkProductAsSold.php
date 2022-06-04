<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToMarkProductAsSold extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao dar baixa no produto , tente novamente. Se o erro persistir , contactar o suporte.";
        $logMessage = "Ocorreu um erro ao dar baixa em um produto.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
