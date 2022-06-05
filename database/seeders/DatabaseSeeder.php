<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        (new PlanSeeder())->run();
        (new RolesSeeder())->run();
        Company::factory()->count(5)->create();
//        Brand::factory()->count(5)->create();
//        Product::factory()->create();
    }
}
