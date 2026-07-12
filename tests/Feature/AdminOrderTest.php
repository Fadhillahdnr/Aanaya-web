<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_orders_page_with_monthly_sales_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $order = Order::create([
            'user_id' => $admin->id,
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '08123456789',
            'address' => 'Jakarta',
            'total_price' => 250000,
            'status' => 'completed',
        ]);

        $order->created_at = now()->startOfYear()->addMonth();
        $order->save();

        $this->actingAs($admin)
            ->get('/admin/orders')
            ->assertOk()
            ->assertViewHas('monthlySales', function ($monthlySales) {
                return $monthlySales->count() === 1
                    && (int) $monthlySales->first()->month === 2
                    && (int) $monthlySales->first()->revenue === 250000;
            });
    }
}
