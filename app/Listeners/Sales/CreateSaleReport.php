<?php

namespace App\Listeners\Sales;

use App\Events\Sales\SaleCreated;
use App\Models\SaleReport;

class CreateSaleReport
{

    /**
     * Handle the event.
     *
     * @param  \App\Events\Sales\SaleCreated  $event
     * @return void
     */
    public function handle(SaleCreated $event): void
    {
        $soldProducts = $event->getSoldProducts();
        $companyId = $event->getCompanyId();
        SaleReport::create()->fromArray($soldProducts->toArray(), $companyId);
    }
}
