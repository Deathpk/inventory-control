<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;

class QuantityToRemoveBiggerThanAvailableQuantity extends AbstractException
{
    public function __construct()
    {
        $responseMessage = "Não há produtos suficientes para dar baixa em estoque, por favor , confira se a quantidade de produtos disponíveis é superior ou igual a quantidade para baixa.";
        parent::__construct(responseMessage: $responseMessage, statusCode: 422);
    }
}
