<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaleItemsTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashierUser;
    protected User $otherCashier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        $cashierRole = Role::create(['name' => 'Cashier', 'description' => 'Cashier']);

        // Users
        $this->cashierUser = User::create([
            'name' => 'Cashier One',
            'email' => 'cashier1@test.com',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'status' => 'active',
        ]);

        $this->otherCashier = User::create([
            'name' => 'Cashier Two',
            'email' => 'cashier2@test.com',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'status' => 'active',
        ]);

        // Product
        $this->product = Product::create([
            'name' => 'Test Product',
            'sku' => 'SKU-001',
            'category_id' => null,
            'price' => 100.00,
            'cost' => 50.00,
            'stock' => 10,
            'reorder_level' => 2,
            'status' => 'active',
        ]);
    }

    public function test_add_item_with_valid_data_updates_subtotal_total_and_stock(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);

        // Start sale
        $saleResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/sales', []);
        $saleResponse->assertStatus(201);
        $saleId = $saleResponse->json('data.sale_id');

        // Add item
        $payload = [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'discount' => 10, // Gross 200 - 10 = 190
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/sales/{$saleId}/items", $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'sale' => [
                             'id',
                             'total_amount',
                             'sale_items',
                         ]
                     ]
                 ]);

        // Verify totals
        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'total_amount' => 190.00,
        ]);

        // Verify stock decreased
        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock);
    }

    public function test_cannot_add_item_to_nonexistent_sale(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/sales/999999/items', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'code' => 'ERR_SALE_NOT_FOUND'
                 ]);
    }

    public function test_cannot_add_item_to_sale_of_another_user(): void
    {
        // Other cashier creates sale
        $otherToken = JWTAuth::fromUser($this->otherCashier);
        $saleResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $otherToken,
        ])->postJson('/api/sales', []);
        $saleId = $saleResponse->json('data.sale_id');

        // First cashier tries to add item
        $token = JWTAuth::fromUser($this->cashierUser);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/sales/{$saleId}/items", [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'code' => 'ERR_UNAUTHORIZED'
                 ]);
    }

    public function test_insufficient_stock_validation(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);
        $saleResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/sales', []);
        $saleId = $saleResponse->json('data.sale_id');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/sales/{$saleId}/items", [
            'product_id' => $this->product->id,
            'quantity' => 999,
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'code' => 'ERR_INSUFFICIENT_STOCK'
                 ]);
    }

    public function test_discount_cannot_exceed_gross_total(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);
        $saleResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/sales', []);
        $saleId = $saleResponse->json('data.sale_id');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/sales/{$saleId}/items", [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'discount' => 500, // Gross is 200, discount too high
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'code' => 'ERR_INVALID_DISCOUNT'
                 ]);
    }

    public function test_price_defaults_to_product_price_when_omitted(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);
        $saleResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/sales', []);
        $saleId = $saleResponse->json('data.sale_id');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/sales/{$saleId}/items", [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $saleId,
            'product_id' => $this->product->id,
            'price' => 100.00,
            'subtotal' => 100.00,
        ]);
    }
}
