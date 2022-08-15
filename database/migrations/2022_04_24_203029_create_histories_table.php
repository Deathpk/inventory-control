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
            $table->enum('entity_type', [History::PRODUCT_ENTITY, History::CATEGORY_ENTITY, History::BRAND_ENTITY]);
            $table->text('metadata');
            $table->foreignIdFor(User::class, 'changed_by_id');
            $table->foreignIdFor(Company::class);
            $table->enum('action_id', [History::PRODUCT_CREATED, History::PRODUCT_UPDATED, History::PRODUCT_DELETED, History::PRODUCT_SOLD, History::ADDED_QUANTITY]);
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
