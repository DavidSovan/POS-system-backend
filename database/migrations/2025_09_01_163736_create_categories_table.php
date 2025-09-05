<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
        });

        // Insert default categories
        DB::table('categories')->insert([
            ['name' => 'General', 'description' => 'General products category', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Food & Beverage', 'description' => 'Food and beverage products', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
