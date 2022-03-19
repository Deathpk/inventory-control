<?php

use App\Models\Product;
use Illuminate\Support\Collection;
use function PHPUnit\Framework\assertTrue;


it('asserts that products can be created with category and brand', function (Collection $product) {
    $response = $this->createProduct($product->toArray())->json();
    expect($response)->toHaveKey('success', true);
})->with('products');

it('asserts that products can be updated with success', function () {
    $firstProductId = Product::first()->id;
    $response = $this->updateProduct($firstProductId)->json();
    expect($response)->toHaveKey('success', true);
});

it('asserts that products can be deleted with success', function () {
    $firstProduct = Product::first()->id;
    $response = $this->deleteProduct($firstProduct)->json();
    expect($response)->toHaveKey('success', true);
});

it('asserts that all products are retrived with success', function () {
    $totalOfProducts = Product::all()->count();
    assertTrue($this->assertDbCount('products', $totalOfProducts));
});

