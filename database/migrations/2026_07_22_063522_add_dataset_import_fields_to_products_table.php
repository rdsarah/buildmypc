<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('original_price_usd', 10, 2)->nullable()->after('price');
            $table->decimal('exchange_rate_used', 8, 2)->nullable()->after('original_price_usd');
            $table->string('source_dataset')->nullable()->after('exchange_rate_used');
            $table->string('source_part_type')->nullable()->after('source_dataset');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'original_price_usd',
                'exchange_rate_used',
                'source_dataset',
                'source_part_type',
            ]);
        });
    }
};