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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['name', 'status']);
        });

        // Insert default supplier
        DB::table('suppliers')->insert([
            [
                'name' => 'Default Supplier',
                'email' => 'supplier@example.com',
                'phone' => '+1234567890',
                'address' => '123 Supplier Street, City, State',
                'contact_person' => 'John Supplier',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
