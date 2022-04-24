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

    protected $fillable = [
        'entity_id',
        'entity_type',
        'action_id',
        'metadata',
        'changed_by_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @throws \Throwable
     */
    public function createChange(array $data): void
    {
        $this->entity_id = $data['entityId'];
        $this->entity_type = $data['entity_type'];
        $this->metadata = $data['metadata'];
        $this->changed_by_id = $data['changedById'];
        $this->action_id = $data['actionId'];
        $this->saveOrFail();
    }
}
