<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SalesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected User $managerUser;
    protected User $cashierUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $adminRole = Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        $managerRole = Role::create(['name' => 'Manager', 'description' => 'Manager']);
        $cashierRole = Role::create(['name' => 'Cashier', 'description' => 'Cashier']);

        // Create users
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        $this->managerUser = User::create([
            'name' => 'Manager User',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role_id' => $managerRole->id,
            'status' => 'active',
        ]);

        $this->cashierUser = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@test.com',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'status' => 'active',
        ]);
    }

    public function test_unauthenticated_user_cannot_start_sale(): void
    {
        $response = $this->postJson('/api/sales', []);
        $response->assertStatus(401);
    }

    public function test_start_sale_uses_authenticated_user_as_cashier_and_sets_initial_values(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/sales', []);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                 ])
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'sale_id',
                         'message',
                     ],
                 ]);

        $saleId = $response->json('data.sale_id');
        $this->assertIsInt($saleId);

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'cashier_id' => $this->cashierUser->id,
            'total_amount' => 0,
        ]);
    }
}
