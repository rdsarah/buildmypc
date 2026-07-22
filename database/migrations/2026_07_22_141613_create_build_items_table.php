<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('build_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('build_id')
                ->constrained('builds')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->timestamps();

            // One selected product per category in one build.
            $table->unique(['build_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_items');
    }
};