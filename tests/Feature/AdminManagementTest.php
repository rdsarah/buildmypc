<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'CPU', 'slug' => 'cpu']);
        $brand = Brand::create(['name' => 'Intel', 'slug' => 'intel']);

        $this->product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Intel Core i9',
            'slug' => 'intel-core-i9',
            'price' => 50000.00,
            'stock' => 15,
            'is_active' => true,
        ]);

        $this->order = Order::create([
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'status' => 'pending',
            'total_price' => 50000.00,
        ]);
    }

    public function test_admin_dashboard_displays_products_and_orders(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Intel Core i9');
        $response->assertSee('Test User');
        $response->assertSee('Admin Dashboard');
    }

    public function test_admin_can_update_product_price_and_stock(): void
    {
        $response = $this->post(route('admin.products.update', $this->product->id), [
            'price' => 48000.00,
            'stock' => 20,
        ]);

        $response->assertRedirect();
        $this->product->refresh();

        $this->assertEquals(48000.00, $this->product->price);
        $this->assertEquals(20, $this->product->stock);
    }

    public function test_admin_can_update_order_status(): void
    {
        $response = $this->post(route('admin.orders.updateStatus', $this->order->id), [
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $this->order->refresh();

        $this->assertEquals('processing', $this->order->status);
    }
}
