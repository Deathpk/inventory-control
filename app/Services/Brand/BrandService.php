<?php


namespace App\Services\Brand;

use App\Exceptions\AbstractException;
use App\Exceptions\Brand\FailedToListBrands;
use App\Exceptions\FailedToCreateEntity;
use App\Exceptions\FailedToDeleteEntity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use mysql_xdevapi\Exception;
use Throwable;

class BrandService
{
    private Brand $brand;

    public function setBrand(Brand $brand): void
    {
        $this->brand = $brand;
    }

    public function createBrand(StoreBrandRequest $request): void
    {
        try {
            $this->storeBrand($request);
        } catch(Throwable $e) {
            throw new FailedToCreateEntity(AbstractException::BRAND_ENTITY_LABEL, $e);
        }
    }

    public function updateBrand(UpdateBrandRequest $request, int $brandId): void
    {
        try {
            $this->updateExistingBrand($request, $brandId);
        } catch(Throwable $e) {
            throw new FailedToCreateEntity(AbstractException::BRAND_ENTITY_LABEL, $e);
        }
    }

    private function storeBrand(StoreBrandRequest $request): void
    {
        Brand::create()->fromRequest($request->getName());
    }


    private function updateExistingBrand(UpdateBrandRequest $request, int $id): void
    {
        /** @var Brand $brand */
        $brand = Brand::find($id);
        if (!$brand) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::BRAND_ENTITY_LABEL, $id);
        }

        $brand->fromRequest($request->getName());
    }

    /**
     * @throws \Exception
     */
    public function getBrand(int $id): Builder|Model
    {
        return Brand::query()->find($id) ??
            throw new RecordNotFoundOnDatabaseException(AbstractException::BRAND_ENTITY_LABEL, $id);
    }

    /**
     * @throws \Throwable
     */
    public function deleteBrand(int $id): void
    {
        try{
            /** @var Brand $brand */
            $brand = Brand::find($id);
            if (!$brand) {
                throw new RecordNotFoundOnDatabaseException(AbstractException::BRAND_ENTITY_LABEL, $id);
            }

            $brand->delete();

        } catch (\Throwable $e) {
            throw new FailedToDeleteEntity(AbstractException::BRAND_ENTITY_LABEL, $e);
        }
    }

    public function listBrands($paginated = false): Collection|LengthAwarePaginator
    {
        try {
            if ($paginated) {
                return Brand::query()->paginate(30);
            }
            return Brand::all();
        } catch(Throwable $e) {
            throw new FailedToListBrands($e);
        }

    }

}
