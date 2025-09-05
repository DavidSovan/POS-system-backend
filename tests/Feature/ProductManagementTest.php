<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected User $managerUser;
    protected User $cashierUser;
    protected Category $category;

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
        
        // Create category
        $this->category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test category for testing',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_product()
    {
        $token = JWTAuth::fromUser($this->adminUser);
        
        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'category_id' => $this->category->id,
            'price' => 10.99,
            'cost' => 5.50,
            'stock' => 100,
            'reorder_level' => 10,
            'description' => 'Test product description',
            'status' => 'active',
        ];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/products', $productData);
        
        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'data' => [
                        'product' => [
                            'name' => 'Test Product',
                            'sku' => 'TEST-001',
                        ]
                    ]
                ]);
        
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
        ]);
    }

    public function test_manager_can_view_products()
    {
        $token = JWTAuth::fromUser($this->managerUser);
        
        // Create a product first
        Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'category_id' => $this->category->id,
            'price' => 10.99,
            'cost' => 5.50,
            'stock' => 100,
            'reorder_level' => 10,
            'status' => 'active',
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/products');
        
        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);
    }

    public function test_cashier_cannot_access_products()
    {
        $token = JWTAuth::fromUser($this->cashierUser);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/products');
        
        $response->assertStatus(403)
                ->assertJson([
                    'status' => 'error',
                    'code' => 'ERR_UNAUTHORIZED',
                ]);
    }

    public function test_unauthenticated_user_cannot_access_products()
    {
        $response = $this->getJson('/api/products');
        
        $response->assertStatus(401);
    }
}
