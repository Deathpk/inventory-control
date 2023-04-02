<?php

namespace App\Services\Reports;

use App\Http\Requests\Reports\ProductsSubtractionReportRequest;
use App\Models\InventoryWriteDownReport;
use App\Policies\FinancialModulePolicy;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProductsSubtractionService
{
    use UsesLoggedEntityId;
    
    private string|null $filterBy;
    private Builder|null $inventoryWriteDownQuery;
    private Collection $productsSubtractions;

    public function __construct(string $filterBy = null)
    {
        $this->filterBy = $filterBy;
        $this->inventoryWriteDownQuery = InventoryWriteDownReport::query()->with('product');

    }

    public function getSubtractionReport(): Collection
    {
        $this->applyFilterToQuery();
        dd($this->inventoryWriteDownQuery->get());
        if(FinancialModulePolicy::acessFinancialModule()) {
            return $this->getAllSubtractedProducts();
        }
    }

    private function getAllSubtractedProducts(): Collection
    {
        return $this->inventoryWriteDownQuery->get();
    }

    private function applyFilterToQuery(): Builder
    {
        switch ($this->filterBy) {
            case ProductsSubtractionReportRequest::WEEKLY_TYPE_FILTER:
                //TODO
                $this->inventoryWriteDownQuery = $this->inventoryWriteDownQuery;
            break;
            case ProductsSubtractionReportRequest::MONTLY_TYPE_FILTER:
                $this->inventoryWriteDownQuery = $this->inventoryWriteDownQuery->whereMonth('created_at', Carbon::now()->month);
            break;
            case ProductsSubtractionReportRequest::YEARLY_TYPE_FILTER:
                $this->inventoryWriteDownQuery = $this->inventoryWriteDownQuery->whereYear('created_at', Carbon::now()->year);
            break;
        }
    }
}