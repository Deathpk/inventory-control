<?php


namespace Tests\Traits\Products;


use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Testing\TestResponse;

trait HasProductActions
{
    use MakesHttpRequests;

    public function createProduct(array $productData): TestResponse
    {
        return $this->post('http://127.0.0.1:8000/api/products/create', $productData);
    }

    public function updateProduct(int $productId): TestResponse
    {
        $productData = [
            'name' => "Produto de teste atualizado :)",
            'quantity' => 15,
            'limitForRestock' => 5,
            'paidPrice' => 2000,
            'sellingPrice' => 3500,
            'categoryName' => 'Eletrônicos',
            'brandName'=> 'Amazon'
        ];

        return $this->put("http://127.0.0.1:8000/api/products/edit/{$productId}", $productData);
    }

    public function deleteProduct(int $productId): TestResponse
    {
        return $this->delete("http://127.0.0.1:8000/api/products/delete/{$productId}");
    }
}
