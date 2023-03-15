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
use Throwable;
use Illuminate\Support\Facades\Log;

class CheckIfSoldProductsNeedsReposition implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SaleCreated $event): void
    {
        $companyData = self::extractCompanyRequiredDataForEmail($event->getCompanyId());
        $soldProductsArray = $event->getSoldProducts();
        $extractedSoldProducts = $this->extractSoldProductsRequiredDataFromArray($soldProductsArray);
        $productsInNeedOfReposition = self::resolveProductsInNeedOfReposition($extractedSoldProducts);

        if ($productsInNeedOfReposition->isNotEmpty()) {
            try {
                Mail::send(new RepositionNeeded($productsInNeedOfReposition, $companyData));
            } catch(Throwable $e) {
                //TODO VC JÁ SABE...
                Log::error("Ocorreu um erro ao disparar o e-mail de notificação de quantidade minima de estoque atingida.\n Id da companhia onde ocorreu o erro: {$event->getCompanyId()}\n Produtos removidos: {$event->getSoldProducts()} \n Error Message: \n {$e->getMessage()} \n StackTrace: {$e->getTraceAsString()}"
                );
            }
        }
    }

    private static function extractCompanyRequiredDataForEmail(int $companyId): array
    {
        $company = Company::query()
            ->where('id', $companyId)
            ->find($companyId, ['name', 'email']);

        return [
          'name' => $company->getName(),
          'email' => $company->getEmail()
        ];
    }

    private function extractSoldProductsRequiredDataFromArray(Collection $soldProductsArray): Collection
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

    private static function resolveProductsInNeedOfReposition(Collection $extractedSoldProducts): Collection
    {
        return $extractedSoldProducts->filter(function (Product $product) {
            return $product->needsReposition();
        });
    }
}
