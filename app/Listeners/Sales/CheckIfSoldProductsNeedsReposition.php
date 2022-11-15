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
        $extractedSoldProducts = self::extractSoldProductsRequiredDataFromArray($soldProductsArray); // TODO FAZER UMA QUERY PARA PEGAR SOMENTE OS DADOS NECESSÁRIOS PARA O ENVIO DO E-MAIL.

        $productsInNeedOfReposition = $this->resolveProductsInNeedOfReposition($extractedSoldProducts);

        if ($productsInNeedOfReposition->isNotEmpty()) {
            Mail::send(new RepositionNeeded($productsInNeedOfReposition, $companyData));
        }
    }

    private static function extractCompanyRequiredDataForEmail(int $companyId): array
    {
        $company = Company::query()
            ->where('id', $companyId)
            ->find($companyId, ['name']);// todo add email when migration is done

        return [
          'name' => $company->getName(),
          'email' => 'bettercallmiguel@gmail.com'
        ];
    }

    private static function extractSoldProductsRequiredDataFromArray(Collection $soldProductsArray): Collection
    {
        return $soldProductsArray->map(function(array $soldProductData) {
            $hasExternalId = array_key_exists('externalProductId', $soldProductData);
            if ($hasExternalId) {
                return Product::with([
                    'brand' => function($query) {
                        $query->select(['id', 'name']);
                    },
                    'category' => function($query) {
                        $query->select(['id', 'name']);
                    }
                ])
                ->where('external_id', $soldProductData['externalProductId'])
                ->get();
            }

             return Product::with([
                 'brand' => function($query) {
                    $query->select(['id', 'name']);
                },
                 'category' => function($query) {
                    $query->select(['id', 'name']);
                 }
             ])
             ->find($soldProductData['productId']);
        });
    }

    private function resolveProductsInNeedOfReposition(Collection &$soldProducts): Collection
    {
        $productsInNeedOfReposition = collect();
        $soldProducts->each(function (Product $product) use(&$productsInNeedOfReposition) {
            if ($product->needsReposition()) {
                $productsInNeedOfReposition->push($product);
            }
        });

        return $productsInNeedOfReposition;
    }
}
