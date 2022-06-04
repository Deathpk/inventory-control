<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $availablePlansMap = collect([
            Plan::getPlanAttributesBasedOnType(Plan::FREE_PLAN_LABEL),
            Plan::getPlanAttributesBasedOnType(Plan::ESSENTIAL_PLAN_LABEL),
            Plan::getPlanAttributesBasedOnType(Plan::PREMIUM_PLAN_LABEL)
        ]);

        $availablePlansMap->each(function (array $planAttributes) {
            DB::table('plans')->insert($planAttributes);
        });
    }
}
