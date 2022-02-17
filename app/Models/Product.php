<?php

namespace App\Models;

use App\Http\Requests\Product\StoreProductRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id;
 * @property string $name
 * @property int $quantity
 * @property int $paid_price
 * @property int $selling_price
 * @property int $limit_for_restock
 * @property int $category_id
 */
class Product extends Model
{
    use HasFactory;

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

    public function fromRequest(StoreProductRequest $request): void
    {
        $this->setProductData($request);

        //TODO MELHORAR SAPORRA
        if (!$request->hasCategoryId()) {
            $this->createCategory($request->getCategoryName());
            $this->setCategory(categoryId: $this->category->getId());
        } else {
            $this->setCategory(categoryId: $request->getCategoryId());
        }

        $this->save();
    }

    private function setProductData(StoreProductRequest $request): void
    {
        $this->name = $request->getName();
        $this->quantity = $request->getQuantity();
        $this->limit_for_restock = $request->getLimitForRestock();
        $this->paid_price = $request->getPaidPrice();
        $this->selling_price = $request->getSellingPrice();
    }

    private function setCategory(int $categoryId): void
    {
        $this->category_id = $categoryId;
    }

    private function createCategory(string $categoryName): void
    {
        $category =  Category::create()->fromProduct($categoryName);
        $this->setCategory(categoryId: $category->getId());
    }
}
