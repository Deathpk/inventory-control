<?php

namespace App\Factories;

class BuyListProduct
{
    public readonly string $name;
    public readonly int $quantity;
    public readonly int $repositionQuantity;

    public function __construct(array $product)
    {
        $this->name = $product['name'];
        $this->quantity = $product['quantity'];
        $this->repositionQuantity = $product['repositionQuantity'];
    }
}
