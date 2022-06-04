<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToCreateProduct extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao criar um produto , tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao criar um produto.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
