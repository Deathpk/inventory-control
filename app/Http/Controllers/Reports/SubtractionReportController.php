<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\ProductsSubtractionReportRequest;
use App\Services\Reports\ProductsSubtractionService;
use Illuminate\Http\JsonResponse;

class SubtractionReportController extends Controller
{
    /**
     * @throws FailedToRetrieveSalesReport
     */
    public function index(ProductsSubtractionReportRequest $request)//: JsonResponse
    {
        $filterBy = $request->getFilterByType();
        $service = new ProductsSubtractionService($filterBy);
        return $service->getSubtractionReport();
    }

    public function mostSubtracted()
    {
        
    }
}
