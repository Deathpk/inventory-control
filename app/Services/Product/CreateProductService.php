<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\FailedToCreateEntity;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\History;
use App\Models\Product;
use App\Models\User;
use App\Services\History\HistoryService;
use App\Traits\History\RegisterHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateProductService
{
    use RegisterHistory;

    private Collection $attributes;
    private int $entityId;

    /**
     * @throws FailedToCreateEntity
     */
    public function createProduct(Collection $attributes): void
    {
        $this->attributes = $attributes;
        try {
            DB::beginTransaction();
            $this->entityId = Product::create()
                ->fromRequest($this->attributes)
                ->getId();
            $this->createProductCreatedHistory();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new FailedToCreateEntity(AbstractException::PRODUCT_ENTITY_LABEL, $e);
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
            'changedById' => self::getChangedBy(),
            'metadata' => $this->getHistoryMetaData()
        ];

        $historyService->createHistory(History::PRODUCT_CREATED, $params);
    }

    private function getHistoryMetaData(): string
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
