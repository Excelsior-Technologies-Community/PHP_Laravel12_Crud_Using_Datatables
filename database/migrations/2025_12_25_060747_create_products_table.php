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
        // Create 'products' table
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('name'); // Product name
            $table->decimal('price', 10, 2); // Product price with 2 decimal points
            $table->text('description')->nullable(); // Product description, nullable

            // ENUM status: 'active' or 'deleted', default is 'active'
            $table->enum('status', ['active', 'deleted'])->default('active');

            // Soft Delete column 'deleted_at' to allow soft deleting
            $table->softDeletes();

            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop 'products' table if it exists
        Schema::dropIfExists('products');
    }
};
