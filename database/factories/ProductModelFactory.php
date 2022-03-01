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
        return [
            'name' => 'HD Seagate Barracuda 2TB',
            'quantity' => 20,
            'paid_price' => 12000,
            'selling_price' => 35000,
            'brand_id' => Brand::query()->first()->id,
            'category_id' => Category::query()->first()->id,
            'limit_for_restock' => 10,
        ];
    }
}
