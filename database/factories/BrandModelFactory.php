<?php

namespace Database\Factories;

use App\Models\Brand;
use Faker\Provider\pt_BR\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BrandModelFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
//        $companies = \App\Models\Company::all();
//        return [
//            'name' => $this->faker->name,
//            'company_id' =>  $companies->random()->first()->id
//        ];
    }
}
