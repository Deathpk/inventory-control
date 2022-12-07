<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        (new PlanSeeder())->run();
        (new RolesSeeder())->run();
        Company::factory()->count(5)->create();
        (new BrandsSeeder())->run();
        (new CategoriesSeeder())->run();
        (new BuyListSeeder())->run();
    }
}
