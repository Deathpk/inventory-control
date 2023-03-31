<?php

namespace App\Models;

use App\Models\Scopes\FilterTenant;
use App\Services\History\HistoryService;
use App\Traits\History\RegisterHistory;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $product_id
 * @property integer $company_id
 * @property integer $write_down_quantity
 * @property integer $report_type
 * 
 */
class InventoryWriteDownReport extends Model
{
    use HasFactory;
    use UsesLoggedEntityId;
    use RegisterHistory;

    const SALES_REPORT_TYPE = 1;
    const INVENTORY_WRITE_DOWN_REPORT_TYPE = 2;
    

    protected $fillable = [
        'product_id',
        'company_id',
        'report_type',
        'write_down_quantity', //Quantidade de baixa em estoque do produto....
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new FilterTenant());
    }

    public function product(): ?BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function company(): ?BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromArray(array $attributes, int $report_type = self::INVENTORY_WRITE_DOWN_REPORT_TYPE): void
    {
        $productId = $this->getProductInstance($attributes)->getId();
        $inventoryWriteDownReport =  $this->resolveInventoryWriteDownReport($productId, $report_type);

        if ($inventoryWriteDownReport) {
            $this->updateWriteDownQuantity($inventoryWriteDownReport, $attributes['soldQuantity'] ?? $attributes['quantityToRemove']);
        } else {
            $this->createNewInventoryWriteDownReport($productId, $attributes['soldQuantity'] ?? $attributes['quantityToRemove']);
        }

        $this->createInventoryWriteDownHistory($productId, $attributes['soldQuantity'] ?? $attributes['quantityToRemove']);
    }

    private function resolveInventoryWriteDownReport(int $productId, int $report_type) 
    {
        return self::query()
        ->where('report_type', $report_type)
        ->where('product_id', $productId)->first();
    }

    private function createNewInventoryWriteDownReport(int &$productId, int $writeDownQuantity): void
    {
        $this->product_id = $productId;
        $this->company_id = self::getLoggedCompanyId();
        $this->write_down_quantity = $writeDownQuantity;
        $this->save();
    }

    private function updateWriteDownQuantity(InventoryWriteDownReport $writeDownReport, int $writeDownQuantity): void
    {
        $writeDownReport->write_down_quantity +=  $writeDownQuantity;
        $writeDownReport->save();
    }

    private function getProductInstance(array &$attributes): Builder|Product
    {
        if (isset($attributes['productId'])) {
            return Product::query()->find($attributes['productId']);
        } else {
            return Product::findByExternalId($attributes['externalProductId']);
        }
    }

    private function createInventoryWriteDownHistory(int $productId, $writeDownQuantity): void
    {
       $historyService = new HistoryService();

       $params =  [
           'entityId' => $productId,
           'entityType' => History::PRODUCT_ENTITY,
           'changedById' => self::getChangedBy(),
           'metadata' => collect([
             'entityId' => $productId,
             'writeDownQuantity' => $writeDownQuantity
            ])->toJson()
       ];

       $historyService->createHistory(History::INVENTORY_WRITE_DOWN, $params);
    }
}
