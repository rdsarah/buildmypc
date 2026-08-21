<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_orders(): void
    {
        $order = Order::create([
            'customer_name' => 'Fahim Rahman',
            'customer_email' => 'fahim@example.com',
            'status' => 'pending',
            'total_price' => 1000.00,
        ]);

        $response = $this->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Fahim Rahman');
        $response->assertSee('pending');
    }

    public function test_customer_can_cancel_pending_order(): void
    {
        Notification::fake();

        $order = Order::create([
            'customer_name' => 'Fahim Rahman',
            'customer_email' => 'fahim@example.com',
            'status' => 'pending',
            'total_price' => 1000.00,
        ]);

        $response = $this->post(route('orders.cancel', $order->id));

        $response->assertRedirect();
        $order->refresh();

        $this->assertEquals('cancelled', $order->status);

        Notification::assertSentOnDemand(
            OrderStatusChanged::class,
            function (OrderStatusChanged $notification, array $channels, $notifiable) use ($order) {
                return $notifiable->routes['mail'] === 'fahim@example.com' &&
                       $notification->order->id === $order->id;
            }
        );
    }

    public function test_customer_can_cancel_confirmed_order(): void
    {
        Notification::fake();

        $order = Order::create([
            'customer_name' => 'Fahim Rahman',
            'customer_email' => 'fahim@example.com',
            'status' => 'confirmed',
            'total_price' => 1000.00,
        ]);

        $response = $this->post(route('orders.cancel', $order->id));

        $response->assertRedirect();
        $order->refresh();

        $this->assertEquals('cancelled', $order->status);

        Notification::assertSentOnDemand(
            OrderStatusChanged::class,
            function (OrderStatusChanged $notification, array $channels, $notifiable) use ($order) {
                return $notifiable->routes['mail'] === 'fahim@example.com' &&
                       $notification->order->id === $order->id;
            }
        );
    }

    public function test_customer_cannot_cancel_shipped_order(): void
    {
        $order = Order::create([
            'customer_name' => 'Fahim Rahman',
            'customer_email' => 'fahim@example.com',
            'status' => 'shipped',
            'total_price' => 1000.00,
        ]);

        $response = $this->post(route('orders.cancel', $order->id));

        $response->assertRedirect();
        $order->refresh();

        $this->assertEquals('shipped', $order->status);
    }
}
