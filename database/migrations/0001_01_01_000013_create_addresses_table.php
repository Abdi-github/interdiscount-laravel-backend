<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('street');
            $table->string('street_number')->nullable();
            $table->string('postal_code');
            $table->string('city');
            $table->string('canton_code', 2)->nullable();
            $table->string('country', 2)->default('CH');
            $table->string('phone')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_billing')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
