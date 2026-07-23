<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('build_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('target_use'); // gaming, editing, office
            $table->timestamps();
        });

        Schema::create('build_template_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('build_template_id')->constrained('build_templates')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_template_products');
        Schema::dropIfExists('build_templates');
    }
};
