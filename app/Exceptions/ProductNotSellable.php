<?php

namespace App\Exceptions;

class ProductNotSellable extends AbstractException
{
    public function __construct()
    {
        $responseMessage = "Um dos produtos não pode ser vendido por não conter um preço de custo e ou de venda.";
        parent::__construct(responseMessage: $responseMessage);
    }
}
