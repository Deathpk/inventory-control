<?php

namespace App\Prototypes\Reports;

use App\Models\ProductSalesReport;

class SaleReport
{
    public int $product_id;
    public string $product_name;
    public int $sold_quantity;
    public int $cost_price;
    public int $profit;

    public static function create(): self
    {
        return new self();
    }

    public function fromArray(ProductSalesReport $saleReport): self
    {
        $this->product_id = $saleReport->product_id;
        $this->product_name = $saleReport->product->name;
        $this->sold_quantity = $saleReport->sold_quantity;
        $this->cost_price = $saleReport->cost_price;
        $this->profit = $saleReport->profit;

        return $this;
    }
}
