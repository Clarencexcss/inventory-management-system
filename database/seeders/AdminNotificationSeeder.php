<?php

namespace Database\Seeders;

use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some existing orders and users for sample notifications
        $orders = Order::with('customer')->take(5)->get();
        $users = User::take(3)->get();

        if ($orders->count() > 0) {
            // Create sample pending order notifications
            foreach ($orders->take(3) as $order) {
                AdminNotification::create([
                    'type' => 'pending_order',
                    'title' => 'New Pending Order',
                    'message' => "New order #{$order->invoice_no} from {$order->customer->name} is pending approval.",
                    'data' => [
                        'order_id' => $order->id,
                        'customer_name' => $order->customer->name,
                        'total_amount' => $order->total ?? 0,
                        'order_date' => $order->order_date?->format('Y-m-d H:i:s'),
                    ],
                    'order_id' => $order->id,
                    'is_read' => false,
                ]);
            }

            // Create sample cancelled order notifications
            if ($orders->count() > 3 && $users->count() > 0) {
                $cancelledOrder = $orders->skip(3)->first();
                $cancelledBy = $users->first();
                
                AdminNotification::create([
                    'type' => 'cancelled_order',
                    'title' => 'Order Cancelled',
                    'message' => "Order #{$cancelledOrder->invoice_no} from {$cancelledOrder->customer->name} has been cancelled by {$cancelledBy->name}.",
                    'data' => [
                        'order_id' => $cancelledOrder->id,
                        'customer_name' => $cancelledOrder->customer->name,
                        'cancelled_by' => $cancelledBy->name,
                        'cancellation_reason' => 'Customer requested cancellation',
                        'cancelled_at' => now()->subHours(2)->format('Y-m-d H:i:s'),
                    ],
                    'order_id' => $cancelledOrder->id,
                    'cancelled_by_user_id' => $cancelledBy->id,
                    'is_read' => false,
                ]);
            }
        }
    }
}
