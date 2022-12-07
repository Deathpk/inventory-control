<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\Scopes\FilterTenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuyListSeeder extends Seeder
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

            $fisrtProduct = Product::query()
            ->withoutGlobalScope(FilterTenant::class)
            ->select('id')
            ->where('company_id', $companyId)
            ->first();

            $buyListData = [
                'productId' => $fisrtProduct['id'],
                'repositionQuantity' => rand(3, 15)
            ];

            DB::table('buy_list')->insert([
                'company_id' => $companyId,
                'products' => collect([$buyListData])->toJson(),
                'created_at' => Carbon::now()
            ]);
        });
    }
}