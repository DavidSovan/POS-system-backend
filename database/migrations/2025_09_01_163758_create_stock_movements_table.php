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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->string('reason'); // e.g., 'sale', 'purchase', 'adjustment', 'damaged', 'lost', 'returned'
            $table->text('notes')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable(); // For 'in' movements
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Who made the movement
            $table->string('reference')->nullable(); // Reference number (invoice, order, etc.)
            $table->integer('stock_after'); // Stock level after this movement
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['type', 'reason']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
