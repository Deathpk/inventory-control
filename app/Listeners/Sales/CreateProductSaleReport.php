<?php

namespace App\Listeners\Sales;

use App\Events\Sales\SaleCreated;
use App\Models\ProductSalesReport;


class CreateProductSaleReport
{
    /**
     * Handle the event.
     *
     * @param SaleCreated $event
     * @return void
     */
    public function handle(SaleCreated $event): void
    {
        $soldProducts = $event->getSoldProducts();

        $soldProducts->each(function (array $soldProduct) {
            ProductSalesReport::create()->fromArray($soldProduct);
        });
    }
}
