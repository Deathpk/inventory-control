<?php

namespace App\Http\Controllers\Product;

use App\Exceptions\Product\FailedToCreateOrUpdateProduct;
use App\Exceptions\Product\FailedToCreateProduct;
use App\Exceptions\Product\FailedToDeleteProduct;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Http\Requests\Product\ImportProductsRequest;
use App\Http\Requests\Product\RemoveSoldUnitRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\AutoComplete\ProductAutoCompleteService;
use App\Services\Product\CreateProductService;
use App\Services\Product\ImportProductService;
use App\Services\Product\ProductService;
use App\Services\Product\RemoveSoldUnitService;
use App\Services\Product\SearchProductService;
use App\Services\Product\UpdateProductService;
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

    public function index(SearchProductService $service): JsonResponse
    {
        $productList = $service->listProducts();
        return response()->json([
            'success' => true,
            'products' => $productList
        ]);
    }

    /**
     * @throws \Exception
     */
    public function show(int $productId, SearchProductService $service): JsonResponse
    {
        $specificProduct = $service->getSpecificProduct($productId);
        return response()->json([
            'success' => true,
            'product' => $specificProduct
        ]);
    }


    /**
     * @throws FailedToCreateProduct
     */
    public function store(StoreProductRequest $request, CreateProductService $service): JsonResponse
    {
        $service->createProduct($request);
        return response()->json([
            'success' => true,
            'message' => 'Produto criado com sucesso!'
        ]);
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function update(int $productId, UpdateProductRequest $request, UpdateProductService $service): JsonResponse
    {
        $service->updateProduct($productId, $request);
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

    /**
     * @throws \Exception
     */
    public function import(ImportProductsRequest $request, ImportProductService $service): JsonResponse
    {
        $service->importProducts($request->getImportedFile());
        return response()->json([
            'success' => true,
            'message' => 'Produtos importados com sucesso!'
        ]);
    }

    /**
     * @throws \Throwable
     * @throws RecordNotFoundOnDatabaseException
     */
    public function removeSoldUnit(RemoveSoldUnitRequest $request, RemoveSoldUnitService $service): JsonResponse
    {
        $service->removeSoldUnit($request);
        return response()->json([
            'success' => true
        ]);
    }
}
