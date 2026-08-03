<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_details_and_order_summary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'name' => 'Dreamy Listener',
            'phone' => '081234567890',
            'address' => 'Bandung, Jawa Barat',
            'gender' => 'female',
            'date_of_birth' => '2000-05-18',
        ]);

        Order::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'total_price' => 275000,
            'status' => 'completed',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertSee('Dreamy Listener')
            ->assertSee('081234567890')
            ->assertSee('Bandung, Jawa Barat')
            ->assertSee('Rp 275.000')
            ->assertSee('Recent Orders');
    }

    public function test_regular_user_is_redirected_away_from_admin_user_details(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.show', $otherUser))
            ->assertRedirect('/');
    }

    public function test_user_management_list_links_to_user_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee(route('admin.users.show', $user), false)
            ->assertSee('Detail');
    }
}
