<?php

namespace App\Models;

use App\Http\Requests\Brand\StoreBrandRequest;
use App\Models\Scopes\FilterTenant;
use App\Traits\UsesLoggedEntityId;
use Database\Factories\BrandModelFactory;
use Database\Factories\ProductModelFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    use UsesLoggedEntityId;


    protected $fillable = [
        'name',
        'company_id'
    ];

    protected $hidden = [
        'deleted_at'
    ];

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

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public static function create(): Brand
    {
        return new Brand();
    }

    public function fromRequest(string $name): void
    {
        $this->setBrandData($name);
    }

    public function fromProduct(string $name): self
    {
        $this->setBrandData($name);
        return $this;
    }

    private function setBrandData(string $name): void
    {
        $this->name = $name;
        $this->company_id = self::getLoggedCompanyId();
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

    public static function findByName(string $name): ?Model
    {
        return Brand::where('name', $name)->first();
    }

    // /**
    //  * Create a new factory instance for the model.
    //  *
    //  * @return Factory
    //  */
    // protected static function newFactory(): Factory
    // {
    //     return BrandModelFactory::new();
    // }
}
