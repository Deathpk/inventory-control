<?php

namespace App\Http\Controllers\Sales;

use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\RemoveSoldProductRequest;
use App\Services\Product\RemoveSoldProductService;
use Illuminate\Http\JsonResponse;

class SalesController extends Controller
{
    /**
     * @throws \Throwable
     * @throws RecordNotFoundOnDatabaseException
     */
    public function removeSoldUnits(RemoveSoldProductRequest $request, RemoveSoldProductService $service): JsonResponse
    {
        $service->removeSoldUnit($request);
        return response()->json([
            'success' => true
        ]);
    }
}
