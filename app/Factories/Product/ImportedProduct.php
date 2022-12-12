<?php

namespace App\Factories\Product;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ImportedProduct
{
    const NAME_KEY = 0;
    const QUANTITY_KEY = 1;
    const MINIMUM_QUANTITY_KEY = 2;
    const PAID_PRICE_KEY = 3;
    const SELLING_PRICE_KEY = 4;
    const CATEGORY_NAME_KEY = 5;
    const BRAND_NAME_KEY = 6;
    const EXTERNAL_PRODUCT_ID_KEY = 7;
    const PRODUCT_DESCRIPTION = 8;

    public string $name;
    public int $quantity;
    public int $minimum_quantity;
    public int $paid_price;
    public int $selling_price;
    public string $category_name;
    public string $brand_name;
    public string|null $external_product_id;
    public string|null $description;

    #[Pure] public static function create(): self
    {
        return new self();
    }

    public function fromArray(array $product): self
    {
        $this->setProductInfo($product);
        return $this;
    }

    private function setProductInfo(array $product): void
    {
        $this->name = $product[self::NAME_KEY];
        $this->quantity = $product[self::QUANTITY_KEY];
        $this->minimum_quantity = $product[self::MINIMUM_QUANTITY_KEY];
        $this->paid_price = self::convertToInteger($product[self::PAID_PRICE_KEY]);
        $this->selling_price = self::convertToInteger($product[self::SELLING_PRICE_KEY]);
        $this->category_name = $product[self::CATEGORY_NAME_KEY];
        $this->brand_name = $product[self::BRAND_NAME_KEY];
        $this->external_product_id = $product[self::EXTERNAL_PRODUCT_ID_KEY] ?? null;
        $this->description = $product[self::PRODUCT_DESCRIPTION] ?? null;
    }

    public function toCollection(): Collection
    {
        return collect([
            'name' => $this->name,
            'quantity' => $this->quantity,
            'minimum_quantity' => $this->minimum_quantity,
            'paidPrice' => $this->paid_price,
            'sellingPrice' => $this->selling_price,
            'categoryName' => $this->category_name,
            'brandName' => $this->brand_name,
            'externalProductId' => $this->external_product_id,
            'description' => $this->description
        ]);
    }

    private static function convertToInteger(float|string $value): int
    {
        if (is_string($value)) {
            $value = str_replace(',', '.',$value);
            // return (int) round(($value * 100), 0);
        }

        return (int) round(($value * 100), 0);
    }

}
