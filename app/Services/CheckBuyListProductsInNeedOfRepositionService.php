<?php

namespace App\Services;

use App\Mail\RepositionNeeded;
use App\Models\BuyList;
use App\Models\Company;
use App\Models\Product;
use App\Models\Scopes\FilterTenant;
use Illuminate\Support\Facades\Mail;

class CheckBuyListProductsInNeedOfRepositionService
{    
    public function checkBuyListOfEachCompany(): void
    {
        //TODO adicionar o where para active true quando a coluna active for implementada.
        $companies = Company::query()
        ->withoutGlobalScope(FilterTenant::class)
        ->select(['id','name', 'email'])
        ->get();

        $companies->each(function (Company $company) {
            $this->checkBuyList($company);
        });
    }

    private function checkBuyList(Company $company): void
    {
        $buyList = BuyList::withoutGlobalScope(FilterTenant::class)
        ->firstWhere('company_id', $company->id);
        
        $productsAtBuyList = collect(json_decode($buyList->products));
        $productsInNeedOfReposition = collect([]);

        $productsAtBuyList->each(function(object $buyListProduct) use(&$productsInNeedOfReposition, $company) {
            $product = $this->getProductInstance($buyListProduct, $company->id);
            $productStillNeedsReposition = $product->quantity <= $product->minimum_quantity;
            
            if ($productStillNeedsReposition) {
                $productsInNeedOfReposition->push($product);
            } 
        });
        
        if ($productsInNeedOfReposition->isNotEmpty()) {
            Mail::send(new RepositionNeeded($productsInNeedOfReposition, $company->toArray()));
        }
    }

    private function getProductInstance(object $buyListProduct, int $companyId): Product
    {
        return property_exists($buyListProduct, 'externalProductId') 
        ? Product::findByExternalIdWithRelationsWithoutTenantFilter($buyListProduct->externalProductId, $companyId)
        : Product::findByIdWithRelationsWithoutTenantFilter($buyListProduct->productId, $companyId);
    }
}