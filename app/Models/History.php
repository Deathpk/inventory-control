<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property int $entity_id
 * @property string $entity_type
 * @property string $metadata
 * @property int $changed_by_id
 * @property int $company_id
 * @property int $action_id
 */
class History extends Model
{
    use HasFactory;

    const PRODUCT_ENTITY = 'product';
    const BRAND_ENTITY = 'brand';
    const CATEGORY_ENTITY = 'category';

    const PRODUCT_CREATED = 1;
    const PRODUCT_UPDATED = 2;
    const PRODUCT_DELETED = 3;
    const PRODUCT_SOLD = 4;
    const ADDED_QUANTITY = 5;

    protected $fillable = [
        'entity_id',
        'entity_type',
        'company_id',
        'action_id',
        'metadata',
        'changed_by_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createChange(int $actionId, array $data): void
    {
        $this->entity_id = $data['entityId'];
        $this->entity_type = $data['entityType'];
        $this->metadata = $data['metadata'];
        $this->changed_by_id = $data['changedById'];
        $this->action_id = $actionId;
        $this->save();
    }
}
