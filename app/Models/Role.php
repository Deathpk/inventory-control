<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    const ADMIN_ROLE = 1;
    const ADMIN_ROLE_LABEL = 'admin';
    const ADMIN_ROLE_PERMISSIONS = ['storeProducts', 'sellProducts', 'importProducts', 'reports', 'customFields'];

    const STOCK_MANAGER_ROLE = 2;
    const STOCK_MANAGER_LABEL = 'stockManager';
    const STOCK_MANAGER_ROLE_PERMISSIONS = ['storeProducts','importProducts','importProducts'];

    protected $fillable = [
        'name',
        'permissions'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function getAvailableRoles(): array
    {
        return [
            'admin' => self::ADMIN_ROLE,
            'stockManager' => self::STOCK_MANAGER_ROLE
        ];
    }

    public static function getAvailableRolesId(): array
    {
        return [
            self::ADMIN_ROLE,
            self::STOCK_MANAGER_ROLE
        ];
    }

    public function getRolePermissions(): array
    {
        return $this->permissions;
    }

    public function getLabel(): string
    {
        $rolesMap = [
            self::ADMIN_ROLE => 'Administrador',
            self::STOCK_MANAGER_ROLE => 'Gerente de estoque'
        ];

        return $rolesMap[$this->id];
    }
}
