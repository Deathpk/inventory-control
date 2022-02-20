<?php

namespace App\Models;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int id;
 * @property string $name
 * @property int $quantity
 * @property int $paid_price
 * @property int $selling_price
 * @property int $limit_for_restock
 * @property int $category_id
 * @property int $brand_id
 * @property Category $category
 */
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'quantity',
        'limit_for_restock',
        'paid_price',
        'selling_price',
        'category_id',
        'brand_id'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public static function create(): self
    {
        return new self();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function fromRequest(Collection $attributes): void
    {
        $this->setProductData($attributes);
        $this->setProductRelations($attributes);
        $this->save();
    }

    private function setProductData(Collection $attributes): void
    {
        $this->name = $attributes->get('name');
        $this->quantity = $attributes->get('quantity');
        $this->limit_for_restock = $attributes->get('limitForRestock');
        $this->paid_price = $attributes->get('paidPrice');
        $this->selling_price = $attributes->get('sellingPrice');
    }

    private function setProductRelations(Collection $attributes): void
    {
        $category = null;
        $brand = null;

        if (!$attributes->get('categoryId')) {
            /** @var Category $category */
            $category = $this->createCategory($attributes->get('categoryName'));
            $category->save();
        }

        $this->setCategory($category?->getId() ?? $attributes->get('categoryId'));

        if (!$attributes->get('brandId')) {
            /** @var Brand $brand */
            $brand = $this->createBrand($attributes->get('brandName'));
            $brand->save();
        }

        $this->setBrand($brand?->getId() ?? $attributes->get('brandId'));
    }

    private function setCategory(int $categoryId): void
    {
        $this->category_id = $categoryId;
    }

    private function createCategory(string $categoryName): Model|BelongsTo
    {
        return $this->category()->create(['name' => $categoryName]);
    }

    private function setBrand(int $brandId): void
    {
        $this->brand_id = $brandId;
    }

    private function createBrand(string $brandName): BelongsTo|Model
    {
        return $this->brand()->create(['name' => $brandName]);
    }
}
