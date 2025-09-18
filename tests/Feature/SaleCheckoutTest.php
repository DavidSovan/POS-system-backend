<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaleCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashierUser;
    protected User $otherCashier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $cashierRole = Role::create(['name' => 'Cashier', 'description' => 'Cashier']);

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

    public function test_successful_checkout_creates_payment_and_marks_sale_completed(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);

        // Start sale
        $saleRes = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/sales', []);
        $saleRes->assertStatus(201);
        $saleId = $saleRes->json('data.sale_id');

        // Add item: 2 x 100 = 200 total
        $addRes = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson("/api/sales/{$saleId}/items", [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
        $addRes->assertStatus(200);

        // Capture stock after item add
        $this->product->refresh();
        $stockAfterAdd = $this->product->stock; // should be 8

        // Checkout with cash amount 200 (exact), expect change 0
        $checkoutRes = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->patchJson("/api/sales/{$saleId}/checkout", [
            'payment_method' => 'cash',
            'amount' => 200,
        ]);

        $checkoutRes->assertStatus(200)
                    ->assertJson([
                        'status' => 'success',
                    ])
                    ->assertJsonStructure([
                        'data' => [
                            'sale' => [
                                'id', 'status', 'payment_method', 'payment' => ['id', 'amount', 'change_given']
                            ]
                        ]
                    ]);

        $this->assertDatabaseHas('payments', [
            'sale_id' => $saleId,
            'method' => 'cash',
            'amount' => 200.00,
            'change_given' => 0.00,
        ]);

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'completed',
            'payment_method' => 'cash',
            'total_amount' => 200.00,
        ]);

        // Stock should remain the same as after adding items (already deducted)
        $this->product->refresh();
        $this->assertEquals($stockAfterAdd, $this->product->stock);
    }

    public function test_insufficient_payment_rejected(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);
        $saleId = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/sales', [])->json('data.sale_id');
        $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson("/api/sales/{$saleId}/items", [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $res = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->patchJson("/api/sales/{$saleId}/checkout", [
            'payment_method' => 'cash',
            'amount' => 150,
        ]);

        $res->assertStatus(422)
            ->assertJson(['code' => 'ERR_INSUFFICIENT_PAYMENT']);
    }

    public function test_cannot_checkout_empty_sale(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);
        $saleId = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/sales', [])->json('data.sale_id');

        $res = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->patchJson("/api/sales/{$saleId}/checkout", [
            'payment_method' => 'card',
            'amount' => 0,
        ]);

        $res->assertStatus(422)
            ->assertJson(['code' => 'ERR_EMPTY_SALE']);
    }

    public function test_cannot_checkout_other_users_sale(): void
    {
        $otherToken = JWTAuth::fromUser($this->otherCashier);
        $saleId = $this->withHeaders(['Authorization' => 'Bearer ' . $otherToken])->postJson('/api/sales', [])->json('data.sale_id');

        $token = JWTAuth::fromUser($this->cashierUser);
        $res = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->patchJson("/api/sales/{$saleId}/checkout", [
            'payment_method' => 'cash',
            'amount' => 0,
        ]);

        $res->assertStatus(403)
            ->assertJson(['code' => 'ERR_UNAUTHORIZED']);
    }

    public function test_cannot_checkout_completed_sale_twice(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);
        $saleId = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/sales', [])->json('data.sale_id');
        $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson("/api/sales/{$saleId}/items", [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);
        $this->withHeaders(['Authorization' => 'Bearer ' . $token])->patchJson("/api/sales/{$saleId}/checkout", [
            'payment_method' => 'card',
            'amount' => 100,
        ])->assertStatus(200);

        $res = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->patchJson("/api/sales/{$saleId}/checkout", [
            'payment_method' => 'card',
            'amount' => 100,
        ]);

        $res->assertStatus(409)
            ->assertJson(['code' => 'ERR_SALE_ALREADY_COMPLETED']);
    }
}
