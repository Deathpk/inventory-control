<?php

namespace App\Events\Products;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UnitRemovedFromInventory
{
    use Dispatchable, SerializesModels;

    protected Collection $products;
    protected int $companyId;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Collection $products, int $companyId)
    {
        $this->products = $products;
        $this->companyId = $companyId;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }
}
