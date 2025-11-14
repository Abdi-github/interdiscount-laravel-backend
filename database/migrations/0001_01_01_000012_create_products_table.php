<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_short')->nullable();
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->string('displayed_code')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('CHF');
            $table->json('images')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->text('specification')->nullable();
            $table->string('availability_state')->default('INSTOCK');
            $table->unsignedSmallInteger('delivery_days')->nullable();
            $table->boolean('in_store_possible')->default(false);
            $table->date('release_date')->nullable();
            $table->json('services')->nullable();
            $table->json('promo_labels')->nullable();
            $table->boolean('is_speed_product')->default(false);
            $table->boolean('is_orderable')->default(true);
            $table->boolean('is_sustainable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('PUBLISHED');
            $table->timestamps();

            $table->index('brand_id');
            $table->index('category_id');
            $table->index('price');
            $table->index('rating');
            $table->index('availability_state');
            $table->index(['status', 'is_active']);
            $table->fullText(['name', 'name_short', 'specification']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
