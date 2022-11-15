<?php

namespace App\Models;

use App\Traits\UsesLoggedEntityId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @property int $company_id
 * @property string $products
 * @property int $total_price
 * @property int $profit
 */
class SaleReport extends Model
{
    use HasFactory;
    use UsesLoggedEntityId;

    protected $fillable = [
        'company_id',
        'products',
        'total_price',
        'profit'
    ];

    protected $table = 'sales_report';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
        'products' => 'array'
    ];

    public function company(): Company|BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromArray(array $soldProducts, int $companyId): void
    {
        try {
            $this->company_id = $companyId;
            $this->products = json_encode($soldProducts);
            $this->resolveSalePriceAndProfit($soldProducts);
            $this->save();
        } catch (Throwable $e) {
            //TODO vc já sabe amigo.
            Log::info($e->getMessage());
            throw $e;
        }
    }

    private function resolveSalePriceAndProfit(array &$soldProducts): void
    {
        $totalPrice = 0;
        $profit = 0;

        collect($soldProducts)->each(function (array $soldProduct) use(&$totalPrice, &$profit) {
            $product = isset($soldProduct['productId'])
                ? Product::find($soldProduct['productId'])
                : Product::findByExternalId($soldProduct['externalProductId']);

            if($product) {
                $sellingPrice = $product->getSellingPrice();
                $costPrice = $product->getCostPrice();

                $totalPrice += $sellingPrice;
                $profit += ($sellingPrice - $costPrice) * $soldProduct['soldQuantity'];
            }
        });

        $this->total_price = $totalPrice;
        $this->profit = $profit;
    }
}
