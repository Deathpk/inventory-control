<?php

namespace App\Listeners;

use App\Events\Products\UnitRemovedFromInventory;
use App\Events\Sales\SaleCreated;
use App\Models\InventoryWriteDownReport;
use Illuminate\Support\Facades\Log;

class CreateInventoryReport
{
    private SaleCreated|UnitRemovedFromInventory $event;
    /**
     * Dependendo da instância de evento que recebermos, chamamos um método diferente. 
     *
     * @param SaleCreated|UnitRemovedFromInventoy $event
     * @return void
     */
    public function handle(SaleCreated|UnitRemovedFromInventory $event): void
    {
        $this->event = $event;
        
        if($this->event instanceof SaleCreated) {
            $this->createSalesReport();
            return;
        }

        $this->createRemovedUnitReport();
    }

    private function createSalesReport(): void 
    {
        $soldProducts = $this->event->getSoldProducts();
        $soldProducts->each(function (array $soldProduct) {
            InventoryWriteDownReport::create()->fromArray($soldProduct, InventoryWriteDownReport::SALES_REPORT_TYPE);
        });
    }

    private function createRemovedUnitReport(): void
    {
        $products = $this->event->getProducts();
        $products->each(function (array $product) {
            InventoryWriteDownReport::create()->fromArray($product, InventoryWriteDownReport::INVENTORY_WRITE_DOWN_REPORT_TYPE);
        });
    }
}
