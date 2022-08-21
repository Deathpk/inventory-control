<?php

namespace App\Models;

use App\Traits\UsesLoggedEntityId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function fromArray(array $attributes, int $companyId): void
    {
        dd($attributes);
    }
}
