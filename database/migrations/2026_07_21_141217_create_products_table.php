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

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('model')->nullable();

            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);

            $table->string('image_url')->nullable();
            $table->text('description')->nullable();

            // PC component specifications
            $table->string('socket_type')->nullable();
            $table->string('ram_type')->nullable();
            $table->string('form_factor')->nullable();
            $table->unsignedInteger('wattage')->nullable();
            $table->string('storage_type')->nullable();
            $table->unsignedInteger('gpu_length_mm')->nullable();
            $table->unsignedInteger('case_clearance_mm')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('price');
            $table->index('stock');
            $table->index('socket_type');
            $table->index('ram_type');
            $table->index('form_factor');
            $table->index('storage_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};