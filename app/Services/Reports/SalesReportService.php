<?php

namespace App\Services\Reports;

use App\Exceptions\Reports\FailedToRetrieveSalesReport;
use App\Http\Requests\Reports\GeneralSalesReportRequest;
use App\Http\Resources\Reports\MostSoldProductResource;
use App\Http\Resources\Reports\ProductSalesReportResource;
use App\Models\InventoryWriteDownReport;
use App\Models\SaleReport;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SalesReportService
{
    use UsesLoggedEntityId;

    private string|null $filterBy;
    private Builder|null $inventoryWriteDownQuery;
    private Builder|null $saleReportsQuery;
    private Builder|Collection|LengthAwarePaginator $allSales;

    public function __construct(string $filterBy = null)
    {
        $this->filterBy = $filterBy;
        $this->inventoryWriteDownQuery = InventoryWriteDownReport::query()->with('product');
        $this->saleReportsQuery = SaleReport::query();
    }

    /**
     * 
     * @throws FailedToRetrieveSalesReport
     */
    public function getSalesReport(): array
    { // TODO REFATORAR.
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
            'sales' => $this->getSalesDetails(),
            'metadata' => $this->getSalesReportMetaData()
        ]);
    }

    private function getSalesDetails(): array
    {
        return $this->allSales->map(function(SaleReport $sale) {
            return [
                'sale_date' => $sale->created_at->format('Y-m-d H:i:s'),
                'products' => json_decode($sale->products),
                'total_price' => $sale->total_price,
                'profit' => $sale->profit
            ];
        })->toArray();
    }

    private function getSalesReportMetaData(): array
    {
        $overallProfit = 0;
        $overallTotalPrice = 0;

        collect($this->allSales)->each(function(SaleReport $sale) use(&$overallProfit, &$overallTotalPrice) {
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
                return $this->saleReportsQuery->whereMonth('created_at', Carbon::now()->month);
            case GeneralSalesReportRequest::YEARLY_TYPE_FILTER:
                return $this->saleReportsQuery->whereYear('created_at', Carbon::now()->year);
        }
    }

    public function getMostSoldProducts(): Collection
    {
        $mostSoldProducts = $this->getLoggedCompanyInstance()
            ->inventoryWriteDownReport()
            ->with('product')
            ->where('report_type', InventoryWriteDownReport::SALES_REPORT_TYPE)
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
