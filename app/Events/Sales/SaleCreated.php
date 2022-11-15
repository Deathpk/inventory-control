<?php

namespace App\Events\Sales;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SaleCreated
{
    use Dispatchable, SerializesModels;

    protected Collection $soldProducts;
    protected int $companyId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Collection $soldProducts, int $companyId)
    {
        $this->soldProducts = $soldProducts;
        $this->companyId = $companyId;
    }

    public function getSoldProducts(): Collection
    {
        return $this->soldProducts;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }
}
