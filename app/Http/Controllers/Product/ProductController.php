<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $service;

    public function __construct()
    {
        $this->service = new ProductService();
    }

    public function index()
    {
        // TODO
    }

    /**
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
     */
    public function destroy(int $productId): JsonResponse
    {
        $this->service->deleteProduct($productId);
        return response()->json([
            'success' => true,
            'message' => 'Produto excluido com sucesso!'
        ]);
    }
}
