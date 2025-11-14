<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->json('display_name')->nullable(); // {de, en, fr, it}
            $table->json('description')->nullable(); // {de, en, fr, it}
            $table->string('resource');
            $table->string('action');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['resource', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
