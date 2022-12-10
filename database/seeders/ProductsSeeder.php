<?php

namespace Database\Seeders;

use App\Models\Company;
use Database\Factories\ProductModelFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
    */
    public function run(): void
    {
        Company::all()->each(function(Company $company) {
            $companyId = $company->getId();
            $product = ProductModelFactory::getMock();
            DB::table('products')->insert([
                'name' => $product['name'],
                'description' => $product['description'],
                'quantity' => $product['quantity'],
                'paid_price' => $product['paid_price'],
                'selling_price' => $product['selling_price'],
                'brand_id' => $product['brand_id'],
                'category_id' => $product['category_id'],
                'minimum_quantity' => $product['minimum_quantity'],
                'company_id' => $companyId
            ]);
        });
    }
}