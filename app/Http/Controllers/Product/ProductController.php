<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
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
        $this->service->createProduct($request);
        return response()->json([
            'success' => true,
            'message' => 'Produto criado com sucesso!'
        ]);
    }

    public function update(UpdateProductRequest $request): JsonResponse
    {
        //TODO
    }

    public function delete()
    {
        //TODO
    }
}
