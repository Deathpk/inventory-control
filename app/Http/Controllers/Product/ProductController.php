<?php

namespace App\Http\Controllers\Product;

use App\Exceptions\FailedToCreateEntity;
use App\Exceptions\FailedToDeleteEntity;
use App\Exceptions\FailedToUpdateEntity;
use App\Exceptions\Product\AttachmentInvalid;
use App\Exceptions\Product\FailedToImportProducts;
use App\Exceptions\Product\FailedToListProducts;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Http\Requests\Product\AddQuantityToStockRequest;
use App\Http\Requests\Product\DeleteProductRequest;
use App\Http\Requests\Product\ImportProductsRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Products\RemoveProductFromInventoryRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Services\AutoComplete\ProductAutoCompleteService;
use App\Services\Product\AddProductQuantityService;
use App\Services\Product\CreateProductService;
use App\Services\Product\DeleteProductService;
use App\Services\Product\ImportProductService;
use App\Services\Product\RemoveProductFromInventoryService;
use App\Services\Product\SearchProductService;
use App\Services\Product\UpdateProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private ProductAutoCompleteService $autoCompleteService;

    public function __construct(ProductAutoCompleteService $autoCompleteService)
    {
        $this->autoCompleteService = $autoCompleteService;
    }

    /**
     * @throws FailedToListProducts
     */
    public function index(SearchProductService $service): ProductCollection
    {
       $products = $service->listProducts();
       return new ProductCollection($products);
    }

    /**
     * @throws \Exception
     */
    public function show(int $productId, SearchProductService $service): ProductResource
    {
        return $service->getSpecificProduct($productId);
    }

    /**
     * @throws FailedToCreateEntity
     */
    public function store(StoreProductRequest $request, CreateProductService $service): JsonResponse
    {
        $service->createProduct($request);
        return response()->json([
            'success' => true,
            'message' => 'Produto criado com sucesso!'
        ]);
    }

    public function removeFromInventory(RemoveProductFromInventoryRequest $request, RemoveProductFromInventoryService $service)
    {
        $service->removeUnitsFromStock($request);
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * @throws RecordNotFoundOnDatabaseException|FailedToUpdateEntity
     */
    public function update(UpdateProductRequest $request, UpdateProductService $service): JsonResponse
    {
        $service->updateProduct($request);
        return response()->json([
            'success' => true,
            'message' => 'Produto atualizado com sucesso!'
        ]);
    }


    /**
     * @throws FailedToDeleteEntity
     */
    public function destroy(DeleteProductRequest $request , DeleteProductService $service): JsonResponse
    {
        $service->deleteProduct($request);
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
        $importedFile = $request->getImportedFile();
        $service->importProducts($importedFile);
        return response()->json([
            'success' => true,
            'message' => 'Produtos importados com sucesso!'
        ]);
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function addToStock(AddQuantityToStockRequest $request, AddProductQuantityService $service): JsonResponse
    {
        $service->addQuantityToStock($request);
        return response()->json([
            'success' => true,
            'message' => 'Quantidade adicionada ao estoque com sucesso!'
        ]);
    }
}
