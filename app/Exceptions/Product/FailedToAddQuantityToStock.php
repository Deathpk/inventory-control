<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToAddQuantityToStock extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao adicionar unidades de um produto ao estoque , por favor , tente novamente!. Caso o erro persista , contacte o suporte.";
        $logMessage = "Ocorreu um ao adicionar unidades de um produto ao estoque.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}