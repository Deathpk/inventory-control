<?php

namespace App\Http\Controllers\Product;

use App\Exceptions\Product\AttachmentInvalid;
use App\Exceptions\Product\FailedToCreateProduct;
use App\Exceptions\Product\FailedToDeleteProduct;
use App\Exceptions\Product\FailedToImportProducts;
use App\Exceptions\Product\FailedToUpdateProduct;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Http\Requests\Product\ImportProductsRequest;
use App\Http\Requests\Product\RemoveSoldProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\AutoComplete\ProductAutoCompleteService;
use App\Services\Product\CreateProductService;
use App\Services\Product\DeleteProductService;
use App\Services\Product\ImportProductService;
use App\Services\Product\RemoveSoldProductService;
use App\Services\Product\SearchProductService;
use App\Services\Product\UpdateProductService;
use Illuminate\Http\JsonResponse;
use JetBrains\PhpStorm\Pure;

class ProductController extends Controller
{
    private ProductAutoCompleteService $autoCompleteService;

    #[Pure] public function __construct(ProductAutoCompleteService $autoCompleteService)
    {
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
     * @throws RecordNotFoundOnDatabaseException|FailedToUpdateProduct
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
    public function destroy(int $productId, DeleteProductService $service): JsonResponse
    {
        $service->deleteProduct($productId);
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
     * @throws FailedToImportProducts|AttachmentInvalid
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
    public function removeSoldUnit(RemoveSoldProductRequest $request, RemoveSoldProductService $service): JsonResponse
    {
        $service->removeSoldUnit($request);
        return response()->json([
            'success' => true
        ]);
    }
}
