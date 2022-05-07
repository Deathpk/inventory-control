<?php

namespace App\Services\Product;

use App\Exceptions\Product\FailedToCreateProduct;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\History;
use App\Models\Product;
use App\Services\History\HistoryService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CreateProductService
{
    private Collection $attributes;
    private int $entityId;

    /**
     * @throws FailedToCreateProduct
     */
    public function createProduct(StoreProductRequest $request): void
    {
        $this->attributes = $request->getAttributes();

        try {
            DB::beginTransaction();
            $this->entityId = Product::create()->fromRequest($this->attributes)->getId();
            $this->createProductCreatedHistory();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new FailedToCreateProduct($e);
        }
    }

    /**
     * @throws \Throwable
     */
    private function createProductCreatedHistory(): void
    {
        $historyService = new HistoryService();

        $params =  [
            'entityId' => $this->entityId,
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => 1,//TODO DEPOIS DE CRIAR O MODULO DE AUTH , RETIRAR ISSO.
            'metadata' => $this->createHistoryMetaData()
        ];

        $historyService->createHistory(History::PRODUCT_CREATED, $params);
    }

    private function createHistoryMetaData(): string
    {
        return collect([
            'entityId' => $this->entityId,
            'productName' => $this->attributes->get('name'),
            'initialQuantity' => $this->attributes->get('quantity'),
            'categoryId' => $this->attributes->get('categoryId'),
            'brandId' => $this->attributes->get('brandId'),
            'limitForRestock' => $this->attributes->get('limitForRestock')
        ])->toJson();
    }
}
