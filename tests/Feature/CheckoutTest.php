<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_cart_explore_products_link_points_to_merchandise_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('href="'.route('merchandise').'"', false)
            ->assertSee('Explore Products');
    }

    public function test_checkout_page_can_be_rendered_with_order_summary(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Aanaya Shirt',
            'slug' => 'aanaya-shirt-checkout-page',
            'image' => 'https://example.com/shirt.jpg',
            'price' => 150000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image,
                        'quantity' => 2,
                    ],
                ],
            ])
            ->get('/checkout')
            ->assertOk()
            ->assertSee('Order Summary')
            ->assertSee('Rp 300.000');
    }

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
            ->assertJsonPath('success', true)
            ->assertJsonPath('order_url', route('orders.show', $response->json('order_id')))
            ->assertJsonStructure(['whatsapp_url']);

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

    public function test_checkout_rejects_insufficient_stock_without_creating_order(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Limited Aanaya Shirt',
            'slug' => 'limited-aanaya-shirt',
            'image' => 'https://example.com/limited.jpg',
            'price' => 200000,
            'stock' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'name' => $product->name,
                        'price' => $product->price,
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
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(1, $product->refresh()->stock);
        $this->assertNotEmpty(session('cart', []));
    }

    public function test_cart_cannot_be_updated_beyond_available_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Aanaya Tote Bag',
            'slug' => 'aanaya-tote-bag',
            'image' => 'https://example.com/tote.jpg',
            'price' => 100000,
            'stock' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image,
                        'quantity' => 1,
                    ],
                ],
            ])
            ->postJson("/cart/update/{$product->id}", ['quantity' => 3]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');

        $this->assertSame(1, session("cart.{$product->id}.quantity"));
    }

    public function test_product_with_variants_requires_a_selection_and_keeps_each_option_separate(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Aanaya Bracelet',
            'slug' => 'aanaya-bracelet',
            'image' => 'https://example.com/bracelet.jpg',
            'price' => 75000,
            'stock' => 5,
            'variant_label' => 'Model',
            'is_active' => true,
        ]);
        $first = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Unfold',
            'stock' => 2,
            'is_active' => true,
        ]);
        $second = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Hanayo',
            'stock' => 3,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('cart.add', $product), [])
            ->assertSessionHasErrors('cart');

        $this->post(route('cart.add', $product), ['variant_id' => $first->id])->assertRedirect();
        $this->post(route('cart.add', $product), ['variant_id' => $second->id])->assertRedirect();

        $cart = session('cart');
        $this->assertCount(2, $cart);
        $this->assertSame('Unfold', $cart["p{$product->id}-v{$first->id}"]['variant_name']);
        $this->assertSame('Hanayo', $cart["p{$product->id}-v{$second->id}"]['variant_name']);
    }

    public function test_variant_checkout_uses_variant_price_decrements_variant_stock_and_saves_snapshot(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Aanaya Shirt',
            'slug' => 'aanaya-shirt-variants',
            'image' => 'https://example.com/shirt.jpg',
            'price' => 150000,
            'stock' => 5,
            'variant_label' => 'Size',
            'is_active' => true,
        ]);
        $small = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'S',
            'sku' => 'SHIRT-S',
            'price' => 140000,
            'stock' => 2,
            'is_active' => true,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'M',
            'stock' => 3,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [
                "p{$product->id}-v{$small->id}" => [
                    'product_id' => $product->id,
                    'variant_id' => $small->id,
                    'name' => $product->name,
                    'price' => 1,
                    'quantity' => 2,
                ],
            ]])
            ->postJson('/checkout/process', [
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'phone' => '08123456789',
                'address' => 'Jalan Test Nomor 123, Jakarta',
            ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total_price' => 280000]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_variant_id' => $small->id,
            'variant_label' => 'Size',
            'variant_name' => 'S',
            'variant_sku' => 'SHIRT-S',
            'price' => 140000,
            'quantity' => 2,
        ]);
        $this->assertSame(0, $small->refresh()->stock);
        $this->assertSame(3, $product->refresh()->stock);
    }
}
