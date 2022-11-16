<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $availableRolesMap = collect([
            self::getRolesAttributesBasedOnType(Role::ADMIN_ROLE_LABEL),
            self::getRolesAttributesBasedOnType(Role::STOCK_MANAGER_LABEL),
        ]);

        $availableRolesMap->each(function (array $roleAttributes) {
            DB::table('roles')->insert($roleAttributes);
        });
    }

    public static function getRolesAttributesBasedOnType(string $roleType): array
    {
        $attributesMap = [
            Role::ADMIN_ROLE_LABEL => [
                'name' => 'administrator',
                'permissions' => json_encode([Plan::STORE_PRODUCTS, Plan::SELL_PRODUCTS, Plan::IMPORT_PRODUCTS, Plan::ACCESS_REPORTS, Plan::CUSTOM_FIELDS]),
            ],
            Role::STOCK_MANAGER_LABEL => [
                'name' => 'stockManager',
                'permissions' => json_encode([Plan::STORE_PRODUCTS, Plan::IMPORT_PRODUCTS, Plan::IMPORT_PRODUCTS]),
            ],
        ];

        return $attributesMap[$roleType];
    }
}
