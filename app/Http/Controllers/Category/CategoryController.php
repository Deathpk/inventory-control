<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $categories = $this->service->listAllCategories();
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->service->createOrUpdateCategory($request);
        return response()->json([
            'success' => true,
            'message' => 'Categoria criada com sucesso!'
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function update(Category $category, UpdateCategoryRequest $request): JsonResponse
    {
        $this->service->createOrUpdateCategory($request, $category);
        return response()->json([
           'success' => true,
           'message' => 'Categoria atualizada com sucesso!'
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->service->deleteCategory($category);
        return response()->json([
            'success' => true,
            'message' => 'Categoria excluida com sucesso!'
        ]);
    }
}
