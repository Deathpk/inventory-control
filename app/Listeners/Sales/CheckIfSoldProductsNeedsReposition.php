<?php

namespace App\Listeners\Sales;

use App\Events\Sales\SaleCreated;
use App\Mail\RepositionNeeded;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class CheckIfSoldProductsNeedsReposition implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SaleCreated $event): void
    {
        $companyData = self::extractCompanyRequiredDataForEmail($event->getCompanyId());
        $soldProductsArray = $event->getSoldProducts();
        $extractedSoldProducts = self::extractSoldProductsRequiredDataFromArray($soldProductsArray);

        $this->resolveProductsInNeedOfReposition($extractedSoldProducts);

        if ($extractedSoldProducts->isNotEmpty()) {
            Mail::send(new RepositionNeeded($extractedSoldProducts, $companyData));
        }
    }

    private static function extractCompanyRequiredDataForEmail(int $companyId): array
    {
        $company = Company::query()
            ->where('id', $companyId)
            ->find($companyId, ['name']);//, 'email'

        return [
          'name' => $company->getName(),
          'email' => 'bettercallmiguel@gmail.com'//$company->getEmail()
        ];
    }

    private static function extractSoldProductsRequiredDataFromArray(Collection $soldProductsArray): Collection
    {
        return $soldProductsArray->map(function(array $soldProductData) {
            $hasExternalId = array_key_exists('externalProductId', $soldProductData);
            if ($hasExternalId) {
                return self::getProductDataBasedOnExternalId($soldProductData['externalProductId']);
            }

            return self::getProductDataBasedOnId($soldProductData['productId']);
        });
    }

    private static function getProductDataBasedOnExternalId(string $externalProductId): Product
    {
        return Product::with([
            'brand' => function($query) {
                $query->select(['id', 'name']);
            },
            'category' => function($query) {
                $query->select(['id', 'name']);
            }
        ])
        ->where('external_product_id', $externalProductId)
        ->first();
    }

    private static function getProductDataBasedOnId(int $id): Product
    {
        return Product::with([
            'brand' => function($query) {
                $query->select(['id', 'name']);
            },
            'category' => function($query) {
                $query->select(['id', 'name']);
            }
        ])->find($id);
    }

    private function resolveProductsInNeedOfReposition(Collection &$soldProducts): void
    {
        $soldProducts->filter(function (Product $product) {
            return $product->needsReposition();
        });
    }
}
