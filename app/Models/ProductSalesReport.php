<?php

namespace App\Models;

use App\Models\Scopes\FilterTenant;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $product_id
 * @property integer $company_id
 * @property integer $sold_quantity
 */
class ProductSalesReport extends Model
{
    use HasFactory;
    use UsesLoggedEntityId;

    protected $fillable = [
        'product_id',
        'company_id',
        'sold_quantity',
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

    public function fromArray(array $attributes): void
    {
        $soldProductId = $this->findSoldProduct($attributes)->getId();
        $productSaleReport = self::query()->firstWhere('product_id', $soldProductId);

        if ($productSaleReport) {
            $this->updateSoldQuantity($productSaleReport, $attributes['soldQuantity']);
        } else {
            $this->createNewSaleReport($soldProductId, $attributes['soldQuantity']);
        }
    }

    private function createNewSaleReport(int &$soldProductId, int &$soldQuantity): void
    {
        $this->product_id = $soldProductId;
        $this->company_id = self::getLoggedCompanyId();
        $this->sold_quantity = $soldQuantity;
        $this->save();
    }

    private function updateSoldQuantity(ProductSalesReport $saleReport, int &$soldQuantity): void
    {
        $saleReport->sold_quantity = $saleReport->sold_quantity + $soldQuantity;
        $saleReport->save();
    }

    private function findSoldProduct(array &$attributes): Builder|Product
    {
        if (isset($attributes['productId'])) {
            return Product::query()->find($attributes['productId']);
        } else {
            return Product::findByExternalId($attributes['externalProductId']);
        }
    }
}
