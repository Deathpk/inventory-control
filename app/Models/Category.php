<?php

namespace App\Models;

use App\Http\Requests\Category\StoreCategoryRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function fromRequest(StoreCategoryRequest $request): self
    {
        $this->setCategoryData(name: $request->getName(), description: $request->getDescription());
        return $this;
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
}
