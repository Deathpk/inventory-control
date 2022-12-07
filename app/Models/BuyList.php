<?php

namespace App\Models;

use App\Models\Scopes\FilterTenant;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property int $company_id
 * @property string $products
 */
class BuyList extends Model
{
    use HasFactory;
    use UsesLoggedEntityId;

    protected $table = 'buy_list';
    protected $fillable = [
        'company_id',
        'products'
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

    public static function create(): self
    {
        return new self();
    }

    public function fromCollection(Collection $attributes): void
    {
        $this->setBuyListData($attributes);
        $this->save();
    }

    private function setBuyListData(Collection $attributes): void
    {
       $this->company_id = self::getLoggedCompanyId();
       $this->products = collect([$attributes->toArray()])->toJson();
    }

    public function addProductToExistingBuyList(Collection $attributes): void
    {
        $existingProducts = collect(json_decode($this->products));
        $existingProducts->push((object) $attributes->toArray());
        $this->products = $existingProducts->toJson();
        $this->save();
    }

    public function updateBuyListProduct(string $updatedBuyListProducts): void
    {
        $this->products = $updatedBuyListProducts;
        $this->save();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
