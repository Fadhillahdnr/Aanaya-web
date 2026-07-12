<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_checkout_cart_successfully(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Aanaya Shirt',
            'slug' => 'aanaya-shirt',
            'image' => 'https://example.com/shirt.jpg',
            'price' => 150000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'name' => $product->name,
                        'price' => 1,
                        'image' => $product->image,
                        'quantity' => 2,
                    ],
                ],
            ])
            ->postJson('/checkout/process', [
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'phone' => '08123456789',
                'address' => 'Jalan Test Nomor 123, Jakarta',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 300000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 300000,
        ]);
        $this->assertSame(3, $product->refresh()->stock);
        $this->assertEmpty(session('cart', []));
    }

    public function test_checkout_returns_clear_error_when_cart_is_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/checkout/process', [])
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'Keranjang belanja kosong.',
            ]);
    }
}
