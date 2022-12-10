<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductModelFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return self::getMock();
    }

    public static function getMock(): array
    {
        $paidPrice = rand(10,10000);
        return [
            'name' => 'Testing Product',
            'description' => 'A testing Product.',
            'quantity' => rand(1,100),
            'paid_price' => $paidPrice,
            'selling_price' => $paidPrice + rand(10,1000),
            'brand_id' => Brand::query()->first()->id,
            'category_id' => Category::query()->first()->id,
            'minimum_quantity' => rand(1,10)
        ];
    }
}
