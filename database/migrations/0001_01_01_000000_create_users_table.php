<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');
            $table->enum('status', ['pending', 'active', 'suspended', 'deactivated'])->default('pending');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['email', 'status']);
            $table->index('role_id');
        });

        // Create default admin user
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');
        
        DB::table('users')->insert([
            'name' => 'System Administrator',
            'email' => 'admin@pos-system.com',
            'password' => Hash::make('Admin@123'),
            'role_id' => $adminRoleId,
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};