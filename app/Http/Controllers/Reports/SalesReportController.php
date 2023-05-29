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
    public function index(GeneralSalesReportRequest $request): JsonResponse
    {
        $filterBy = $request->getFilterByType();
        $filterValue = $request->getFilterValue();
        $service = new SalesReportService($filterBy, $filterValue);
        return response()->json($service->getSalesReport());
    }

    public function mostSoldProduct(GeneralSalesReportRequest $request): JsonResponse
    {
        $filterBy = $request->getFilterByType();
        $filterValue = $request->getFilterValue();
        $service = new SalesReportService($filterBy, $filterValue);
        $mostSoldProducts = $service->getMostSoldProducts();
        return response()->json([
            'success' => true,
            'mostSold' => $mostSoldProducts
        ]);
    }
}
