<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaleReceiptTest extends TestCase
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

    public function test_cashier_can_view_own_sale_receipt(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);

        // Start sale
        $saleRes = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/sales', []);
        $saleRes->assertStatus(201);
        $saleId = $saleRes->json('data.sale_id');

        // Add items: 2 x 100 = 200 total
        $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson("/api/sales/{$saleId}/items", [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ])->assertStatus(200);

        // Checkout with cash amount 200
        $this->withHeaders(['Authorization' => 'Bearer ' . $token])->patchJson("/api/sales/{$saleId}/checkout", [
            'payment_method' => 'cash',
            'amount' => 200,
        ])->assertStatus(200);

        // Fetch receipt
        $res = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->getJson("/api/sales/{$saleId}");
        $res->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'data' => [
                    'sale' => ['id', 'status', 'payment_method', 'total_amount', 'created_at'],
                    'items' => [
                        ['product_id', 'product_name', 'quantity', 'price', 'discount', 'subtotal']
                    ],
                    'payment' => ['id', 'method', 'amount', 'change_given'],
                    'cashier' => ['id', 'name'],
                ]
            ]);

        $this->assertEquals(200.0, (float) $res->json('data.sale.total_amount'));
        $this->assertEquals('completed', $res->json('data.sale.status'));
        $this->assertEquals('cash', $res->json('data.sale.payment_method'));
        $this->assertEquals('Test Product', $res->json('data.items.0.product_name'));
        $this->assertEquals(2, $res->json('data.items.0.quantity'));
        $this->assertEquals(0.0, (float) $res->json('data.payment.change_given'));
        $this->assertEquals($this->cashierUser->id, $res->json('data.cashier.id'));
    }

    public function test_cashier_cannot_view_others_sale_receipt(): void
    {
        $otherToken = JWTAuth::fromUser($this->otherCashier);
        $saleId = $this->withHeaders(['Authorization' => 'Bearer ' . $otherToken])->postJson('/api/sales', [])->json('data.sale_id');

        $token = JWTAuth::fromUser($this->cashierUser);
        $res = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->getJson("/api/sales/{$saleId}");
        $res->assertStatus(403)
            ->assertJson(['code' => 'ERR_UNAUTHORIZED']);
    }

    public function test_receipt_returns_404_for_missing_sale(): void
    {
        $token = JWTAuth::fromUser($this->cashierUser);
        $res = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->getJson('/api/sales/999999');
        $res->assertStatus(404)
            ->assertJson(['code' => 'ERR_SALE_NOT_FOUND']);
    }
}
