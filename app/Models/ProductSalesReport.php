<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property integer $product_id
 * @property integer $sold_quantity
 * @property integer $cost_price,
 * @property integer $profit
 */
class ProductSalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sold_quantity',
        'cost_price',
        'profit'
    ];
    protected $dateFormat = 'Y-m-d H:i:s';

    public function product(): ?HasOne
    {
        return $this->hasOne(Product::class);
    }

    public static function create(array $attributes): void
    {
        /** @var Product $soldProduct */
        $soldProduct = Product::find($attributes['productId']);
        $costPrice = $soldProduct->getCostPrice();
        $sellingPrice = $soldProduct->getSellingPrice();

        $newInstance = new self();
        $newInstance->product_id = $attributes['productId'];
        $newInstance->sold_quantity = $attributes['soldQuantity'];
        $newInstance->cost_price = $costPrice;
        $newInstance->profit = ($sellingPrice - $costPrice) * $attributes['soldQuantity'];
        $newInstance->save();
    }
}
