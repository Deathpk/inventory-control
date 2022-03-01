<?php

namespace App\Models;

use App\Http\Requests\Category\StoreCategoryRequest;
use Database\Factories\CategoryModelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int $id;
 * @property string $name
 * @property string $description
 */
class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function create(): self
    {
        return new self();
    }

    public function fromRequest(Collection $attributes): void
    {
        $this->setCategoryData($attributes->get('name'), $attributes->get('description'));
    }

    public function fromProduct(string $newCategoryName): self
    {
        $this->setCategoryData(name: $newCategoryName, description: null);
        return $this;
    }

    private function setCategoryData(string $name, ?string $description): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->save();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public static function findByName(string $name): ?Model
    {
        return Category::where('name', $name)->first();
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return CategoryModelFactory::new();
    }
}
