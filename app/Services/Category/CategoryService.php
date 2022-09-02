<?php


namespace App\Services\Category;

use App\Exceptions\AbstractException;
use App\Exceptions\Category\FailedToListCategories;
use App\Exceptions\FailedToDeleteEntity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Brand;
use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Throwable;

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
          throw new RecordNotFoundOnDatabaseException(AbstractException::CATEGORY_ENTITY_LABEL, $id);
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
            throw new FailedToDeleteEntity(AbstractException::CATEGORY_ENTITY_LABEL, $e);
        }
    }

    public function listAllCategories($paginated = false): LengthAwarePaginator|Collection
    {
        try {

            if ($paginated) {
                return Category::query()->paginate(30);
            }
    
            return Category::all();

        } catch(Throwable $e) {
            throw new FailedToListCategories($e);
        }
    }
}
