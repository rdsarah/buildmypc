<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('source_name')->nullable()->after('is_active');
            $table->string('source_id')->nullable()->after('source_name');
            $table->string('source_category')->nullable()->after('source_id');
            $table->json('specs_json')->nullable()->after('source_category');

            $table->index(['source_name', 'source_category']);
            $table->unique(['source_name', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['source_name', 'source_id']);
            $table->dropIndex(['source_name', 'source_category']);

            $table->dropColumn([
                'source_name',
                'source_id',
                'source_category',
                'specs_json',
            ]);
        });
    }
};