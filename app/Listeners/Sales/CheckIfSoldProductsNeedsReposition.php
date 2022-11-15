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
        $extractedSoldProducts = self::extractSoldProductsFromArray($soldProductsArray); // TODO FAZER UMA QUERY PARA PEGAR SOMENTE OS DADOS NECESSÁRIOS PARA O ENVIO DO E-MAIL.

        $productsInNeedOfReposition = $this->resolveProductsInNeedOfReposition($extractedSoldProducts);

        if ($productsInNeedOfReposition->isNotEmpty()) {
            Mail::send(new RepositionNeeded($productsInNeedOfReposition, $companyData));
        }
        // TODO CHECAR SE CADA PRODUTO QUE FOI VENDIDO CHEGOU NO LIMITE DE REPOSIÇÃO , SE SIM , DISPARAR E-MAIL PARA A COMPANHIA E USUÁRIOS.
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

    private static function extractSoldProductsFromArray(Collection $soldProductsArray): Collection
    {
        return $soldProductsArray->map(function(array $soldProductData) {
            $hasExternalId = array_key_exists('externalProductId', $soldProductData);
            if ($hasExternalId) {
                return Product::findByExternalId($soldProductData['externalProductId']);
            }

             return Product::find($soldProductData['productId']);
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
