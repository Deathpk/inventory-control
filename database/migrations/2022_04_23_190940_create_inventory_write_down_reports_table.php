<?php

use App\Models\Company;
use App\Models\InventoryWriteDownReport;
use App\Models\Product;
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
        Schema::create('inventory_write_down_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Product::class)->constrained();
            $table->foreignIdFor(Company::class)->constrained();
            $table->enum('report_type', [InventoryWriteDownReport::SALES_REPORT_TYPE, InventoryWriteDownReport::INVENTORY_WRITE_DOWN_REPORT_TYPE]);
            $table->integer('write_down_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_write_down_reports');
    }
};
