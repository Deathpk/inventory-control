<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class SoldQuantityBiggerThanAvailableQuantity extends AbstractException
{
    #[Pure] public function __construct()
    {
        $responseMessage = "Não há produtos suficientes para essa venda , por favor , confira se a quantidade de produtos disponíveis é superior ou igual a quantidade de venda.";
        parent::__construct(responseMessage: $responseMessage, statusCode: 422);
    }
}
