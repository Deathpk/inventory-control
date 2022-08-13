<?php


namespace App\Services\Category;


use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Brand;
use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
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

    /**
     * @throws Exception
     */
    public function getCategory(int $id): Builder|Model
    {
        return Category::query()->find($id) ??
            throw new \Exception(
                'Categoria não encontrada no banco de dados.'
            );
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

    public function listAllCategories($paginated = false): LengthAwarePaginator|Collection
    {
        if ($paginated) {
            return Category::query()->paginate(30);
        }

        return Category::all();
    }
}
