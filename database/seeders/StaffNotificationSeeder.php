<?php

namespace Database\Seeders;

use App\Models\StaffNotification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some sample orders and users
        $orders = Order::with('customer')->take(5)->get();
        $users = User::where('role', 'staff')->take(2)->get();
        
        if ($orders->isEmpty()) {
            $this->command->info('No orders found. Skipping staff notification seeding.');
            return;
        }
        
        // Create sample pending order notifications
        foreach ($orders as $order) {
            StaffNotification::create([
                'type' => 'pending_order',
                'title' => 'New Pending Order',
                'message' => "New order #{$order->invoice_no} from {$order->customer->name} is pending approval.",
                'data' => [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'total_amount' => $order->total,
                    'order_date' => $order->order_date->format('Y-m-d H:i:s'),
                ],
                'order_id' => $order->id,
                'is_read' => false,
            ]);
        }
        
        // Create sample cancelled order notifications
        if ($orders->count() > 3 && $users->count() > 0) {
            $cancelledOrder = $orders->skip(3)->first();
            $cancelledBy = $users->first();
            
            StaffNotification::create([
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
        
        // Create sample low stock notifications
        StaffNotification::create([
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => 'Product Beef Sirloin (Code: BEEF001) is running low. Current stock: 5 kg.',
            'data' => [
                'product_id' => 1,
                'product_name' => 'Beef Sirloin',
                'product_code' => 'BEEF001',
                'current_stock' => 5,
                'unit' => 'kg',
            ],
            'is_read' => false,
        ]);
        
        $this->command->info('Staff notifications seeded successfully!');
    }
}