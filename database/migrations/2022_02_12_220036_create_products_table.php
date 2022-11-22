<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('external_product_id')->nullable();
            $table->string('name', 120);
            $table->string('description')->nullable();
            $table->integer('quantity');
            $table->integer('paid_price');
            $table->integer('selling_price');
            $table->foreignIdFor(Company::class)->constrained();
            $table->foreignIdFor(Brand::class)->constrained();
            $table->foreignIdFor(Category::class)->constrained();
            $table->integer('minimum_quantity');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
