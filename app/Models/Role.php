<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    const ADMIN = 1;
    const SALESMAN = 2;
    const STORE_KEEPER = 3;

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
            'admin' => self::ADMIN,
            'salesman' => self::SALESMAN,
            'storeKeeper' => self::STORE_KEEPER
        ];
    }
}
