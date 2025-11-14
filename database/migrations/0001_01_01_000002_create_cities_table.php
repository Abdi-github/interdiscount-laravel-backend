<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {de, en, fr, it}
            $table->string('slug')->unique();
            $table->foreignId('canton_id')->constrained('cantons')->cascadeOnDelete();
            $table->json('postal_codes'); // ["8001", "8002"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('canton_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
