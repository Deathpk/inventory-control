<?php

namespace App\Http\Controllers\Reports;

use App\Exceptions\Reports\FailedToRetrieveSalesReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\GeneralSalesReportRequest;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\JsonResponse;

class SalesReportController extends Controller
{
    /**
     * @throws FailedToRetrieveSalesReport
     */
    public function index(GeneralSalesReportRequest $request)//: JsonResponse
    {
        $filterBy = $request->getFilterByType();
        $service = new SalesReportService($filterBy);
        return $service->getSalesReport();
    }

    public function mostSoldProduct(GeneralSalesReportRequest $request): JsonResponse
    {
        $filterBy = $request->getFilterByType();
        $service = new SalesReportService($filterBy);
        $mostSoldProducts = $service->getMostSoldProducts();
        return response()->json([
            'success' => true,
            'mostSold' => $mostSoldProducts
        ]);
    }
}
