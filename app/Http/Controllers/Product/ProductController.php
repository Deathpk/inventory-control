<?php

namespace App\Http\Controllers\Product;

use App\Exceptions\Product\FailedToCreateOrUpdateProduct;
use App\Exceptions\Product\FailedToDeleteProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\AutoComplete\ProductAutoCompleteService;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use JetBrains\PhpStorm\Pure;

class ProductController extends Controller
{
    private ProductService $service;
    private ProductAutoCompleteService $autoCompleteService;

    #[Pure] public function __construct(ProductService $service, ProductAutoCompleteService $autoCompleteService)
    {
        $this->service = $service;
        $this->autoCompleteService = $autoCompleteService;
    }

    public function index(): JsonResponse
    {
        $productList = $this->service->listProducts();
        return response()->json([
            'success' => true,
            'products' => $productList
        ]);
    }


    /**
     * @throws FailedToCreateOrUpdateProduct
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->service->createOrUpdateProduct($request);
        return response()->json([
            'success' => true,
            'message' => 'Produto criado com sucesso!'
        ]);
    }

    /**
     * @throws FailedToCreateOrUpdateProduct
     */
    public function update(int $productId, UpdateProductRequest $request): JsonResponse
    {
        $this->service->createOrUpdateProduct($request, $productId);
        return response()->json([
            'success' => true,
            'message' => 'Produto atualizado com sucesso!'
        ]);
    }


    /**
     * @throws FailedToDeleteProduct
     */
    public function destroy(int $productId): JsonResponse
    {
        $this->service->deleteProduct($productId);
        return response()->json([
            'success' => true,
            'message' => 'Produto excluido com sucesso!'
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
