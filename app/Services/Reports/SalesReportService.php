<?php

namespace App\Services\Reports;

use App\Exceptions\Reports\FailedToRetrieveSalesReport;
use App\Models\Product;
use App\Models\ProductSalesReport;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SalesReportService
{
    use UsesLoggedEntityId;

    /**
     * @throws FailedToRetrieveSalesReport
     */
    public function getAllSales(): LengthAwarePaginator
    {
        try {
            return ProductSalesReport::query()
                ->where('company_id', self::getLoggedCompanyId())
                ->paginate(15);
        } catch(\Throwable $e) {
            throw new FailedToRetrieveSalesReport($e);
        }
    }

    public function getMostSoldProduct()
    {
        try {
            return $this->resolveMostSoldProduct();
        } catch (\Throwable $e) {
            throw new FailedToRetrieveSalesReport($e);
        }
    }

    private function resolveMostSoldProduct(): Product
    {
        $allSalesReports = ProductSalesReport::query()
            ->where('company_id', self::getLoggedCompanyId())
            ->select(['product_id', 'sold_quantity'])
            ->get();

        $mostSoldProduct = $allSalesReports->map(function (ProductSalesReport) {
            //TODO
        });
//        $mostSoldProduct = ProductSalesReport::query()
//            ->withMax('product', 'sold_quantity')->get();
//        dd($mostSoldProduct);
    }
}
