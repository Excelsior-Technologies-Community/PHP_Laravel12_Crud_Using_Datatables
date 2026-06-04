<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->unique()->nullable()->after('id');
            $table->integer('stock_quantity')->default(0)->after('price');
            $table->string('brand')->nullable()->after('description');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('price');
            $table->date('expiry_date')->nullable()->after('status');
            $table->string('featured_image')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'stock_quantity', 'brand', 'discount_percentage', 'expiry_date', 'featured_image']);
        });
    }
};