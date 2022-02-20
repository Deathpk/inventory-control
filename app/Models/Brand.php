<?php

namespace App\Models;

use App\Http\Requests\Brand\StoreBrandRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 */
class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function create(): Brand
    {
        return new Brand();
    }

    public function fromRequest(StoreBrandRequest $request): self
    {
        $this->setBrandData($request->getName());
        return $this;
    }

    public function fromProduct(string $name): self
    {
        $this->setBrandData($name);
        return $this;
    }

    private function setBrandData(string $name): void
    {
        $this->name = $name;
        $this->save();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
