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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->decimal('price', 10, 2); // Selling price
            $table->decimal('cost', 10, 2)->nullable(); // Purchase cost
            $table->integer('stock')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->text('description')->nullable();
            $table->string('barcode')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->timestamps();

            $table->index(['sku', 'status']);
            $table->index(['category_id', 'status']);
            $table->index('stock');
            $table->index('reorder_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
