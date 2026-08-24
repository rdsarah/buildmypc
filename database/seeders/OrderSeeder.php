<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::create([
            'order_number'   => 'ORD-' . strtoupper(uniqid()),
            'customer_name'  => 'Fahim Rahman',
            'customer_email' => 'fahim@example.com',
            'status'         => 'pending',
            'total_amount'   => 0.00,
        ]);
    }
}