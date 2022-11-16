<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    const ADMIN_ROLE = 1;
    const ADMIN_ROLE_LABEL = 'admin';

    const STOCK_MANAGER_ROLE = 2;
    const STOCK_MANAGER_LABEL = 'stockManager';

    protected $fillable = [
        'name',
        'permissions'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public static function getAvailableRoles(): array
    {
        return [
            'admin' => self::ADMIN_ROLE,
            'stockManager' => self::STOCK_MANAGER_ROLE
        ];
    }
}
