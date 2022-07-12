<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->setUpFaker();
        Company::all()->each(function (Company $company) {
            DB::table('categories')->insert([
                'name' => $this->faker->word,
                'description' => $this->faker->realTextBetween(10, 120),
                'company_id' => $company->getId()
            ]);
        });
    }
}
