<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property PersonalAccessToken $token
 */
class Company extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'cnpj',
        'plan_id',
    ];

    public static function create(): self
    {
        return new self();
    }

    public function fromArray(array $data): self
    {
        $this->name = $data['companyName'];
        $this->cnpj = $data['companyCnpj'];
        $this->email = $data['email'];
        $this->plan_id = $data['planId'];
        $this->save();
        return $this;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function plan(): HasOne
    {
        return $this->hasOne(Plan::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function salesReport(): ?HasMany
    {
        return $this->hasMany(ProductSalesReport::class);
    }

    public function buyList(): HasOne
    {
        return $this->hasOne(BuyList::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPlanId(): int
    {
        return $this->plan()->getId();
    }

    public function canGenerateApiToken(): bool
    {
        //TODO DESCOMENTAR DPS...
        return true;//! ($this->getPlanId() === Plan::FREE_PLAN);
    }

    public function specificTokenExists(int $id): bool
    {
        return $this->tokens()->where('id', $id)->exists();
    }

    public function revokeSelectedToken(int $id): void
    {
        $this->tokens()->where('id', $id)->delete();
    }
}
