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

    const SALESMAN_ROLE = 2;
    const SALESMAN_ROLE_LABEL = 'salesman';

    const STORE_KEEPER_ROLE = 3;
    const STORE_KEEPER_ROLE_LABEL = 'storeKeeper';

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
            'salesman' => self::SALESMAN_ROLE,
            'storeKeeper' => self::STORE_KEEPER_ROLE
        ];
    }
}
