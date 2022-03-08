<?php


namespace App\Services\Category;


use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Brand;
use App\Models\Category;
use Exception;
use Illuminate\Support\Collection;

class CategoryService
{
    private Category $category;

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function createOrUpdateCategory(StoreCategoryRequest|UpdateCategoryRequest $request, Category $category = null): void
    {
        try {
            switch ($request) {
                case $request instanceof StoreCategoryRequest:
                    $this->storeCategory($request);
                    break;
                case $request instanceof UpdateCategoryRequest:
                    $this->updateCategory($request, $category);
                    break;
            }
        } catch (\Throwable $e) {
            throw $e;
            //TODO Criar custom exceptipn
        }
    }

    private function storeCategory(StoreCategoryRequest $request): void
    {
        $attributes = $request->getAttributes();
        Category::create()->fromRequest($attributes);
    }

    private function updateCategory(UpdateCategoryRequest $request, Category $category): void
    {
        $attributes = $request->getAttributes();
        $category->fromRequest($attributes);
    }

    public function deleteCategory(int $category): void
    {
        try {
            $category = Category::find($category);
            if (!$category) {
                throw new Exception('Categoria não encontrada no DB.');
            }

            $category->delete();
        } catch (\Throwable $e) {
            throw new $e; //TODO CRIAR CUSTOM EXCEPTION
        }
    }

    public function listAllCategories(): Collection
    {
        return Category::all();
    }
}
