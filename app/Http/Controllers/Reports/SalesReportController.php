<?php

namespace App\Http\Controllers\Reports;

use App\Exceptions\Reports\FailedToRetrieveSalesReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\GeneralSalesReportRequest;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class SalesReportController extends Controller
{
    /**
     * @throws FailedToRetrieveSalesReport
     */
    public function index(GeneralSalesReportRequest $request)//: JsonResponse
    {
        $filterBy = $request->getFilterByType();
        $service = new SalesReportService($filterBy);

        if (!Auth::check()) {
            throw new UnauthorizedException();
        }

        return $service->getSalesReport();
    }

    public function mostSoldProduct(SalesReportService $service): JsonResponse
    {
        //TODO
        if (!Auth::check()) {
            throw new UnauthorizedException();
        }

        $mostSoldProducts = $service->getMostSoldProducts();
        return response()->json([
            'success' => true,
            'mostSold' => $mostSoldProducts
        ]);
    }
}
