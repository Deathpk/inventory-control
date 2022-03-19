<?php


namespace App\Services\Brand;


use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use mysql_xdevapi\Exception;

class BrandService
{
    private Brand $brand;

    public function setBrand(Brand $brand): void
    {
        $this->brand = $brand;
    }

    /**
     * @throws \Throwable
     */
    public function createOrUpdateBrand(StoreBrandRequest|UpdateBrandRequest $request, int $brandId = null): void
    {
        try{
            switch ($request) {
                case $request instanceof StoreBrandRequest: $this->storeBrand($request);break;
                case $request instanceof UpdateBrandRequest: $this->updateBrand($request, $brandId);break;
            }
        } catch (\Throwable $e) {
            throw $e; //TODO CRIAR CUSTOM EXCEPTION
        }

    }

    private function storeBrand(StoreBrandRequest $request): void
    {
        Brand::create()->fromRequest($request->getName());
    }


    private function updateBrand(UpdateBrandRequest $request, int $id): void
    {
        /** @var Brand $brand */
        $brand = Brand::find($id);
        if (!$brand) {
            throw new \Exception('Marca não encontrada no banco de dados.');
            //TODO CRIAR CUSTOM EXCEPTION
        }

        $brand->fromRequest($request->getName());
    }

    /**
     * @throws \Exception
     */
    public function getBrand(int $id): Builder|Model
    {
        return Brand::query()->find($id) ??
            throw new \Exception(
                'Marca não encontrada no banco de dados.'
            );
    }

    /**
     * @throws \Throwable
     */
    public function deleteBrand(int $brandId): void
    {
        try{
            /** @var Brand $brand */
            $brand = Brand::find($brandId);
            if (!$brand) {
                throw new Exception('Marca não encontrada no DB.');
            }

            $brand->delete();

        } catch (\Throwable $e) {
            throw $e;//TODO CRIAR CUSTOM EXCEPTION
        }
    }

    public function listBrands(): Collection
    {
        return Brand::all();
    }

}
