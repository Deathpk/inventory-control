<?php

namespace App\Http\Controllers\Reports;

use App\Exceptions\Reports\FailedToRetrieveSalesReport;
use App\Http\Controllers\Controller;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class SalesReportController extends Controller
{
    /**
     * @throws FailedToRetrieveSalesReport
     */
    public function index(SalesReportService $service): JsonResponse
    {
        if (!Auth::check()) {
            throw new UnauthorizedException();
        }

        return response()->json([
           'success' => true,
            'sales' => $service->getAllSales()
        ]);
    }

    public function mostSoldProduct(SalesReportService $service): JsonResponse
    {
        if (!Auth::check()) {
            throw new UnauthorizedException();
        }

        return response()->json([
            'success' => true,
            'mostSold' => $service->getMostSoldProduct()
        ]);
    }
}
