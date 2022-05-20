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
            $table->string('name', 120);
            $table->integer('quantity');
            $table->integer('paid_price');
            $table->integer('selling_price');
            $table->foreignIdFor(Company::class);
            $table->foreignIdFor(Brand::class);
            $table->foreignIdFor(Category::class);
            $table->integer('limit_for_restock');
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
