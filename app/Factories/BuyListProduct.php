<?php

namespace App\Factories;

class BuyListProduct
{
    public readonly string $id;
    public readonly bool $external;
    public readonly string $name;
    public readonly int $quantity;
    public readonly int $repositionQuantity;

    public function __construct(array $product)
    {
        $this->id = $product['id'] ?? $product['external_product_id'];
        $this->external = isset($product['external_product_id']);
        $this->name = $product['name'];
        $this->quantity = $product['quantity'];
        $this->repositionQuantity = $product['repositionQuantity'];
    }
}
