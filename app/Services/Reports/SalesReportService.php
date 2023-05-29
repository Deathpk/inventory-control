<?php

namespace App\Services\Reports;

use App\Exceptions\Reports\FailedToRetrieveSalesReport;
use App\Http\Requests\Reports\GeneralSalesReportRequest;
use App\Http\Resources\Reports\MostSoldProductResource;
use App\Models\InventoryWriteDownReport;
use App\Models\SaleReport;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SalesReportService
{
    use UsesLoggedEntityId;

    private string|null $filterBy;
    private string|null $filterValue;
    private Builder|null $inventoryWriteDownQuery;
    private Builder|null $saleReportsQuery;
    private Builder|Collection $allSales;

    public function __construct(string $filterBy = null, string $filterValue = null)
    {
        $this->filterBy = $filterBy;
        $this->filterValue = $filterValue;
        $this->inventoryWriteDownQuery = InventoryWriteDownReport::query()
        ->where('report_type', InventoryWriteDownReport::SALES_REPORT_TYPE)
        ->with('product');
        $this->saleReportsQuery = SaleReport::query();
    }

    /**
     * 
     * @throws FailedToRetrieveSalesReport
     */
    public function getSalesReport(): array
    {
        try {
            $this->allSales = $this->getAllSales();
            return $this->prepareSalesReportForResponse()->toArray();
        } catch(\Throwable $e) {
            throw new FailedToRetrieveSalesReport($e);
        }
    }

    private function prepareSalesReportForResponse(): Collection
    {
        return collect([
            'sales' => $this->getSalesDetails()->paginate(10),
            'metadata' => $this->getSalesReportMetaData()
        ]);
    }

    private function getSalesDetails(): Collection
    {
        return $this->allSales->map(function(SaleReport $sale) {
            return [
                'sale_date' => $sale->created_at->format('Y-m-d H:i:s'),
                'products' => json_decode($sale->products),
                'total_price' => $sale->total_price,
                'profit' => $sale->profit
            ];
        });
    }

    private function getSalesReportMetaData(): array
    {
        $overallProfit = 0;
        $overallTotalPrice = 0;

        collect($this->allSales)
        ->each(function(SaleReport $sale) use(&$overallProfit, &$overallTotalPrice) {
            $overallProfit += $sale->profit;
            $overallTotalPrice += $sale->total_price;
        });

       return [
         'overallProfit' => $overallProfit,
         'overallTotalPrice' => $overallTotalPrice
       ];
    }

    private function getAllSales(): Collection
    {
      return $this->applyFilterToSalesQuery()->get();
    }

    private function applyFilterToSalesQuery(): Builder
    {
        switch ($this->filterBy) {
            case GeneralSalesReportRequest::WEEKLY_TYPE_FILTER:
                //TODO
                return $this->saleReportsQuery;
            case GeneralSalesReportRequest::MONTLY_TYPE_FILTER:
                return $this->saleReportsQuery->whereMonth('created_at', $this->filterValue);
            case GeneralSalesReportRequest::YEARLY_TYPE_FILTER:
                return $this->saleReportsQuery->whereYear('created_at', $this->filterValue);
        }
    }

    private function applyFilterToInventoryWritedownQuery(): Builder
    {
        switch ($this->filterBy) {
            case GeneralSalesReportRequest::WEEKLY_TYPE_FILTER:
                //TODO
                return $this->inventoryWriteDownQuery;
            case GeneralSalesReportRequest::MONTLY_TYPE_FILTER:
                return $this->inventoryWriteDownQuery->whereMonth('created_at', $this->filterValue);
            case GeneralSalesReportRequest::YEARLY_TYPE_FILTER:
                return $this->inventoryWriteDownQuery->whereYear('created_at', $this->filterValue);
        }
    }

    public function getMostSoldProducts(): Collection
    {
        $this->applyFilterToInventoryWritedownQuery();
        $mostSoldProducts = $this->inventoryWriteDownQuery
            ->orderBy('write_down_quantity','desc')
            ->take(3)
            ->get();

        return $this->resolveMostSoldProducts($mostSoldProducts);
    }

    private function resolveMostSoldProducts(Collection $allSaleReports): Collection
    {
        return $allSaleReports->map(function (InventoryWriteDownReport $report) {
            return MostSoldProductResource::make($report);
        });
    }
}