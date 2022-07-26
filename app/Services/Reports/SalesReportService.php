<?php

namespace App\Services\Reports;

use App\Exceptions\Reports\FailedToRetrieveSalesReport;
use App\Http\Resources\ProductSalesReportCollection;
use App\Http\Resources\ProductSalesReportResource;
use App\Models\Product;
use App\Models\ProductSalesReport;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SalesReportService
{
    use UsesLoggedEntityId;

    /**
     * @throws FailedToRetrieveSalesReport
     */
    public function getSalesReport(): AnonymousResourceCollection
    {
        try {
            $allSales = $this->getAllSales();
            return ProductSalesReportResource::collection($allSales);
        } catch(\Throwable $e) {
            throw new FailedToRetrieveSalesReport($e);
        }
    }

    private function getAllSales(): LengthAwarePaginator
    {
        return  ProductSalesReport::query()
            ->with('product')
            ->where('company_id', self::getLoggedCompanyId())
            ->paginate(10);
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

        $mostSoldProduct = $allSalesReports->map(function (ProductSalesReport $eoq) {
            //TODO
        });
//        $mostSoldProduct = ProductSalesReportResource::query()
//            ->withMax('product', 'sold_quantity')->get();
//        dd($mostSoldProduct);
    }
}
