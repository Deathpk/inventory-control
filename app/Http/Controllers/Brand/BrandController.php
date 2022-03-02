<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Services\Brand\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\Pure;

class BrandController extends Controller
{
    private BrandService $service;

    #[Pure] public function __construct()
    {
        $this->service = new BrandService();
    }

    public function index(): JsonResponse
    {
        $brands = $this->service->listBrands();
        return response()->json([
            'success' => true,
            'brands' => $brands
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $this->service->createOrUpdateBrand($request);
        return response()->json([
            'success' => true,
            'message' => 'Marca criada com sucesso!'
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function update(int $brandId, UpdateBrandRequest $request): JsonResponse
    {
        $this->service->createOrUpdateBrand($request, $brandId);
        return response()->json([
            'success' => true,
            'message' => 'Marca atualizada com sucesso!'
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function destroy(int $brandId): JsonResponse
    {
        $this->service->deleteBrand($brandId);
        return response()->json([
            'success' => true,
            'message' => 'Marca excluida com sucesso!'
        ]);
    }
}
