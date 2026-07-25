<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // Links to User (nullable allows guests, or remove ->nullable() if login is required)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Links to Product
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            $table->integer('rating');
            $table->text('comment');
            $table->timestamps();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
