<?php

namespace App\Models;

use App\Models\Scopes\FilterTenant;
use App\Traits\UsesLoggedEntityId;
use Database\Factories\ProductModelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int id;
 * @property string $name
 * @property string $description
 * @property int $quantity
 * @property int $paid_price
 * @property int $selling_price
 * @property string $external_product_id
 * @property int $limit_for_restock
 * @property int $category_id
 * @property int $brand_id
 * @property int $company_id
 * @property Category $category
 */
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesLoggedEntityId;

    protected $fillable = [
        'name',
        'quantity',
        'limit_for_restock',
        'paid_price',
        'selling_price',
        'external_product_id',
        'category_id',
        'brand_id',
        'company_id'
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

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public static function create(): self
    {
        return new self();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function fromRequest(Collection $attributes): self
    {
        $this->setProductData($attributes);
        $this->setProductRelations($attributes);
        $this->save();

        return $this;
    }

    private function setProductData(Collection $attributes): void
    {
        $this->name = $attributes->get('name') ?? $this->name;
        $this->description = $attributes->get('description') ?? $this->description;
        $this->quantity = $attributes->get('quantity') ?? $this->quantity;
        $this->limit_for_restock = $attributes->get('limitForRestock') ?? $this->limit_for_restock;
        $this->paid_price = $attributes->get('paidPrice') ?? $this->paid_price;
        $this->selling_price = $attributes->get('sellingPrice') ?? $this->selling_price;
        $this->external_product_id = $attributes->get('externalProductId') ?? $this->external_product_id;
        $this->company_id = self::getLoggedCompanyId();
    }

    private function setProductRelations(Collection $attributes): void
    {
        $category = null;
        $brand = null;

        if (!$attributes->get('categoryId')) {
            $categoryName = $attributes->get('categoryName') ?? $this->category->name;
            $category = $this->createCategory($categoryName);
            $category->save();
        }

        $this->setCategory($category?->getId() ?? $attributes->get('categoryId'));

        if (!$attributes->get('brandId')) {
            $brandName = $attributes->get('brandName') ?? $this->brand->name;
            $brand = $this->createBrand($brandName);
            $brand->save();
        }

        $this->setBrand($brand?->getId() ?? $attributes->get('brandId'));
    }

    private function setCategory(int $categoryId): void
    {
        $this->category_id = $categoryId;
    }

    private function createCategory(string $categoryName): Model|Collection
    {
        $existingCategory = Category::findByName($categoryName);

        if ($existingCategory) {
            return $existingCategory;
        }

        return $this->category()->create([
            'name' => $categoryName,
            'company_id' => self::getLoggedCompanyId()
        ]);
    }

    private function setBrand(int $brandId): void
    {
        $this->brand_id = $brandId;
    }

    private function createBrand(string $brandName): Model|Collection
    {
        $existingBrand = Brand::findByName($brandName);

        if ($existingBrand) {
            return $existingBrand;
        }

        return $this->brand()->create([
            'name' => $brandName,
            'company_id' => self::getLoggedCompanyId()
        ]);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return ProductModelFactory::new();
    }

    public function removeSoldUnit(int $quantitySold): void
    {
        $this->quantity = $this->quantity - $quantitySold;
        $this->save();
    }

    public function addQuantity(int $quantityToAdd): void
    {
        $this->quantity = $this->quantity + $quantityToAdd;
        $this->save();
    }

    public function getCostPrice(): int
    {
        return $this->paid_price;
    }

    public function getSellingPrice(): int
    {
        return $this->selling_price;
    }

    public static function findByExternalId(string $externalProductId): Builder|Product|null
    {
        return Product::query()->firstWhere('external_product_id', $externalProductId);
    }
}
