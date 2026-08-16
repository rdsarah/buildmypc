<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('is_active', true)->get();

        if ($products->isEmpty()) {
            return;
        }

        $ordersData = [
            [
                'customer_name' => 'Fahim Rahman',
                'customer_email' => 'zamanfaiza1923@gmail.com',
                'status' => 'pending',
            ],
            [
                'customer_name' => 'Anika Tabassum',
                'customer_email' => 'zamanfaiza1923@gmail.com',
                'status' => 'processing',
            ],
            [
                'customer_name' => 'Tanvir Islam',
                'customer_email' => 'zamanfaiza1923@gmail.com',
                'status' => 'shipped',
            ],
            [
                'customer_name' => 'Sadia Afrin',
                'customer_email' => 'zamanfaiza1923@gmail.com',
                'status' => 'delivered',
            ],
        ];

        foreach ($ordersData as $data) {
            // Pick 1-3 random products
            $orderProducts = $products->random(rand(1, 3));
            $totalPrice = 0;

            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'status' => $data['status'],
                'total_price' => 0, // Will calculate below
            ]);

            foreach ($orderProducts as $product) {
                $qty = rand(1, 2);
                $price = $product->price;
                $totalPrice += $price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                ]);
            }

            $order->update(['total_price' => $totalPrice]);
        }
    }
}
