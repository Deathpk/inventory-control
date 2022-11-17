<?php

namespace Database\Factories;

use App\Models\Plan;
use Faker\Provider\pt_BR\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        /** @var Company $fakerCompany */
        $fakePtBrCompany = App::make(Company::class);

        return [
            'name' => $this->faker->company,
            'cnpj' => $fakePtBrCompany->cnpj(),
            'email' => $this->faker->email(),
            'plan_id' => rand(1,2)
        ];
    }
}
