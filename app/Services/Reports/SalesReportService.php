<?php

namespace App\Services\Reports;

use App\Exceptions\Reports\FailedToRetrieveSalesReport;
use App\Http\Requests\Reports\GeneralSalesReportRequest;
use App\Http\Resources\Reports\MostSoldProductResource;
use App\Http\Resources\Reports\MostSoldProductsReportCollection;
use App\Http\Resources\Reports\ProductSalesReportResource;
use App\Models\Product;
use App\Models\ProductSalesReport;
use App\Prototypes\Reports\SaleReport;
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
    private Builder|null $salesReportQuery;
    private AnonymousResourceCollection $allSales;

    public function __construct(string $filterBy = null)
    {
        $this->filterBy = $filterBy;
        $this->salesReportQuery = ProductSalesReport::query()->with('product');
    }

    /**
     * @throws FailedToRetrieveSalesReport
     */
    public function getSalesReport(): array
    {
        try {
            $this->allSales = ProductSalesReportResource::collection($this->getAllSales());
            return $this->prepareSalesReportForResponse();
        } catch(\Throwable $e) {
            throw new FailedToRetrieveSalesReport($e);
        }
    }

    private function prepareSalesReportForResponse(): array
    {
        $metadata = $this->getSalesReportMetaData();

        return [
            'sales' => $this->allSales,
            'metadata' => $metadata
        ];
    }

    private function getSalesReportMetaData(): array
    {
        $overallProfit = 0;
        $overallCost = 0;

        collect($this->allSales)->each(function(array $sale) use(&$overallProfit, &$overallCost) {
            $overallProfit += $sale['profit'];
            $overallCost += $sale['cost_price'];
        });

       return [
         'overallProfit' => $overallProfit,
         'overallCost' => $overallCost
       ];
    }

    private function getAllSales(): LengthAwarePaginator
    {
      return $this->applyFilterToSalesQuery()->paginate(10);
    }

    private function applyFilterToSalesQuery(): Builder
    {
        switch ($this->filterBy) {
            case GeneralSalesReportRequest::WEEKLY_TYPE_FILTER:
                //TODO
                return $this->salesReportQuery;
            case GeneralSalesReportRequest::MONTLY_TYPE_FILTER:
                return $this->salesReportQuery->whereMonth('created_at', Carbon::now()->month);
            case GeneralSalesReportRequest::YEARLY_TYPE_FILTER:
                return $this->salesReportQuery->whereYear('created_at', Carbon::now()->year);
        }
    }

    public function getMostSoldProducts(): Collection
    {
        $mostSoldProducts = $this->getLoggedCompanyInstance()
            ->salesReport()
            ->with('product')
            ->orderBy('sold_quantity','desc')
            ->take(3)
            ->get();

        return $this->resolveMostSoldProducts($mostSoldProducts);
    }

    private function resolveMostSoldProducts(Collection $allSaleReports): Collection
    {
        return $allSaleReports->map(function (ProductSalesReport $report) {
            return MostSoldProductResource::make($report);
        });
    }
}
