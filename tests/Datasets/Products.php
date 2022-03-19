<?php

dataset('products', function () {
    return [
        collect([
            'name' => 'HD 1TB Seagate Barracuda',
            'quantity' => 15,
            'paidPrice' => 13000,
            'sellingPrice' => 23000,
            'categoryName' => 'Informática',
            'brandName' => 'Seagate',
            'limitForRestock' => 8
        ]),
        collect([
            'name' => 'Mesa 4 lugares',
            'quantity' => 5,
            'paidPrice' => 15000,
            'sellingPrice' => 28000,
            'categoryName' => 'Casa',
            'brandName' => 'MadeiraMadeira',
            'limitForRestock' => 3
        ]),
        collect([
            'name' => 'Kindle 10a',
            'quantity' => 10,
            'paidPrice' => 18000,
            'sellingPrice' => 32000,
            'categoryName' => 'Eletrônicos',
            'brandName' => 'Amazon',
            'limitForRestock' => 5
        ])
    ];
});
