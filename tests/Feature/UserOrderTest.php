<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_sees_their_own_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownOrder = $this->createOrder($user);
        $otherOrder = $this->createOrder($otherUser);

        $this->actingAs($user)
            ->get('/orders')
            ->assertOk()
            ->assertSee("Order #{$ownOrder->id}")
            ->assertDontSee("Order #{$otherOrder->id}");
    }

    public function test_user_can_view_and_track_their_order(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, 'shipped');

        $this->actingAs($user)
            ->get("/orders/{$order->id}")
            ->assertOk()
            ->assertSee("Order #{$order->id}")
            ->assertSee('Order Tracking')
            ->assertSee('Order shipped');
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $user = User::factory()->create();
        $otherOrder = $this->createOrder(User::factory()->create());

        $this->actingAs($user)
            ->get("/orders/{$otherOrder->id}")
            ->assertNotFound();
    }

    public function test_user_can_poll_only_their_own_order_status(): void
    {
        $user = User::factory()->create();
        $ownOrder = $this->createOrder($user, 'processing');
        $otherOrder = $this->createOrder(User::factory()->create(), 'shipped');

        $this->actingAs($user)
            ->getJson("/orders/{$ownOrder->id}/status")
            ->assertOk()
            ->assertJsonPath('status', 'processing')
            ->assertJsonStructure(['updated_at']);

        $this->actingAs($user)
            ->getJson("/orders/{$otherOrder->id}/status")
            ->assertNotFound();
    }

    private function createOrder(User $user, string $status = 'pending'): Order
    {
        $product = Product::create([
            'name' => 'Aanaya Test Merch',
            'slug' => 'aanaya-test-merch-'.fake()->uuid(),
            'image' => 'https://example.com/product.jpg',
            'price' => 125000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '08123456789',
            'address' => 'Jalan Aanaya Nomor 123, Jakarta',
            'total_price' => 125000,
            'status' => $status,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'price' => 125000,
            'quantity' => 1,
            'subtotal' => 125000,
        ]);

        return $order;
    }
}
