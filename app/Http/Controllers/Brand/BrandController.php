<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandResource;
use App\Services\AutoComplete\BrandAutoCompleteService;
use App\Services\AutoComplete\Interfaces\AutoCompleteService;
use App\Services\Brand\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\Pure;

class BrandController extends Controller
{
    private BrandService $service;
    private BrandAutoCompleteService $autoCompleteService;

    public function __construct(BrandService $service, BrandAutoCompleteService $autoCompleteService)
    {
        $this->service = $service;
        $this->autoCompleteService = $autoCompleteService;
    }

    public function index(): BrandCollection
    {
        $brands = $this->service->listBrands(true);
        return new BrandCollection($brands);
    }

    public function show(int $brandId): BrandResource
    {
        $specificBrand = $this->service->getBrand($brandId);
        return BrandResource::make($specificBrand);
    }

    /**
     * @throws \Throwable
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $this->service->createBrand($request);
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
        $this->service->updateBrand($request, $brandId);
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

    public function autoComplete(AutoCompleteRequest $request): JsonResponse
    {
        $results = $this->autoCompleteService->retrieveResults($request);
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
