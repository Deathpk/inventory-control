<?php

namespace App\Prototypes\Product;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;

class ImportedProduct
{
    const NAME_KEY = 0;
    const QUANTITY_KEY = 1;
    const LIMIT_FOR_RESTOCK_KEY = 2;
    const PAID_PRICE_KEY = 3;
    const SELLING_PRICE_KEY = 4;
    const CATEGORY_NAME_KEY = 5;
    const BRAND_NAME_KEY = 6;

    public string $name;
    public int $quantity;
    public int $limit_for_restock;
    public int $paid_price;
    public int $selling_price;
    public string $category_name;
    public string $brand_name;

    #[Pure] public static function create(): self
    {
        return new self();
    }

    public function fromArray(array $product): self
    {
        $this->setProductInfo($product);
        return $this;
    }

    public function toCollection(): Collection
    {
        return collect([
            'name' => $this->name,
            'quantity' => $this->quantity,
            'limitForRestock' => $this->limit_for_restock,
            'paidPrice' => $this->paid_price,
            'sellingPrice' => $this->selling_price,
            'categoryName' => $this->category_name,
            'brandName' => $this->brand_name
        ]);

    }

    private function setProductInfo(array $product): void
    {
        $this->name = $product[self::NAME_KEY];
        $this->quantity = $product[self::QUANTITY_KEY];
        $this->limit_for_restock = $product[self::LIMIT_FOR_RESTOCK_KEY];
        $this->paid_price = $product[self::PAID_PRICE_KEY];
        $this->selling_price = $product[self::SELLING_PRICE_KEY];
        $this->category_name = $product[self::CATEGORY_NAME_KEY];
        $this->brand_name = $product[self::BRAND_NAME_KEY];
    }

}
