<?php

namespace App\Models;

use App\Models\Scopes\FilterTenant;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property integer $product_id
 * @property integer $company_id
 * @property integer $sold_quantity
 * @property integer $cost_price,
 * @property integer $profit
 */
class ProductSalesReport extends Model
{
    use HasFactory;
    use UsesLoggedEntityId;

    protected $fillable = [
        'product_id',
        'company_id',
        'sold_quantity',
        'cost_price',
        'profit'
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

    public function fromArray(array $attributes)
    {
        $soldProduct = $this->findSoldProduct($attributes);
        $costPrice = $soldProduct->getCostPrice();
        $sellingPrice = $soldProduct->getSellingPrice();

        $this->product_id = $soldProduct->getId();
        $this->company_id = self::getLoggedCompanyId();
        $this->sold_quantity = $attributes['soldQuantity'];
        $this->cost_price = $costPrice;
        $this->profit = ($sellingPrice - $costPrice) * $attributes['soldQuantity'];
        $this->save();
    }

    private function findSoldProduct(array &$attributes): Product
    {
        if (isset($attributes['productId'])) {
            return Product::find($attributes['productId']);
        } else {
            return Product::findByExternalId($attributes['externalProductId']);
        }
    }
}
