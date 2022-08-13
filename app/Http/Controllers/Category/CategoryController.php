<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\AutoComplete\CategoryAutoCompleteService;
use App\Services\AutoComplete\Interfaces\AutoCompleteService;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    private CategoryService $service;
    private CategoryAutoCompleteService $autocompleteService;

    public function __construct(CategoryService $service, CategoryAutoCompleteService $autoCompleteService)
    {
        $this->service = $service;
        $this->autocompleteService = $autoCompleteService;
    }

    public function index(): CategoryCollection
    {
        $categories = $this->service->listAllCategories(true);
        return new CategoryCollection($categories);
    }

    /**
     * @throws \Exception
     */
    public function show(int $categoryId): CategoryResource
    {
        $specificCategory = $this->service->getCategory($categoryId);
        return CategoryResource::make($specificCategory);
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
    public function destroy(int $categoryId): JsonResponse
    {
        $this->service->deleteCategory($categoryId);
        return response()->json([
            'success' => true,
            'message' => 'Categoria excluida com sucesso!'
        ]);
    }

    public function autoComplete(AutoCompleteRequest $request): JsonResponse
    {
        $results = $this->autocompleteService->retrieveResults($request);
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
