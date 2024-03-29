<?php

use App\Models\Company;
use App\Models\History;
use App\Models\User;
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
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('entity_id');
            $table->enum('entity_type', [History::PRODUCT_ENTITY, History::CATEGORY_ENTITY, History::BRAND_ENTITY, History::USER_ENTITY, History::COMPANY_ENTITY]);
            $table->text('metadata');
            $table->foreignIdFor(User::class, 'changed_by_id');
            $table->foreignIdFor(Company::class)->constrained();
            $table->enum('action_id', [History::PRODUCT_CREATED, History::PRODUCT_UPDATED, History::PRODUCT_DELETED, History::INVENTORY_WRITE_DOWN, History::ADDED_QUANTITY, History::USER_PASSWORD_CHANGED]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
