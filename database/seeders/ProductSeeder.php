<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {

            for ($i = 1; $i <= 3; $i++) {
                Product::create([
                    'barcode' => '2213123',
                    'order_barcode' => $order->barcode,
                    'quantity' => rand(1, 10),
                ]);
            }
        }

    }
}
