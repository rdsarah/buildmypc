<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('builds', function (Blueprint $table) {
            $table->id();

            // For future login feature. For now, builds are session-based.
            $table->unsignedBigInteger('user_id')->nullable();

            // This lets guests create a build before authentication exists.
            $table->string('session_id')->nullable()->index();

            $table->string('name')->default('My Custom PC Build');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->integer('estimated_wattage')->default(0);

            // draft for current build, saved for future saved-build feature
            $table->string('status')->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('builds');
    }
};