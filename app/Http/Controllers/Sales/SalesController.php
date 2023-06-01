<?php

namespace App\Http\Controllers\Sales;

use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\RemoveSoldProductRequest;
use App\Mail\RepositionNeeded;
use App\Services\Product\RemoveSoldProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SalesController extends Controller
{
    /**
     * @throws \Throwable
     * @throws RecordNotFoundOnDatabaseException
     */
    public function removeSoldUnits(RemoveSoldProductRequest $request, RemoveSoldProductService $service): JsonResponse
    {
        $service->removeSoldUnitsFromStock($request->getAttributes());
        return response()->json([
            'success' => true
        ]);
    }
}
