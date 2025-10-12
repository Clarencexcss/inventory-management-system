<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Customer;
use App\Services\CustomerNotificationService;
use App\Enums\OrderStatus;

class TestOrderStatusChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-status-change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test order status change notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Order Status Change Notifications...');
        
        // Get the test order
        $order = Order::latest()->first();
        if (!$order) {
            $this->error('No orders found. Please create an order first.');
            return;
        }
        
        $this->info("Using order: #{$order->invoice_no} (ID: {$order->id})");
        $this->info("Current status: {$order->order_status->value}");
        
        $customer = $order->customer;
        $notificationService = app(CustomerNotificationService::class);
        
        // Get initial notification count
        $initialCount = $notificationService->getUnreadCount($customer);
        $this->info("Initial unread notifications: {$initialCount}");
        
        $this->info('\n--- Testing Status Change: Pending to Complete ---');
        
        // Ensure order is pending first
        if ($order->order_status !== OrderStatus::PENDING) {
            $order->update(['order_status' => OrderStatus::PENDING]);
            $this->info('Reset order to pending status');
        }
        
        // Change status to complete
        $order->update(['order_status' => OrderStatus::COMPLETE]);
        $this->info('✅ Order status changed to complete');
        
        // Check if notification was created
        $newCount = $notificationService->getUnreadCount($customer);
        $this->info("New unread notifications: {$newCount}");
        
        if ($newCount > $initialCount) {
            $this->info('✅ Notification created for order completion!');
            
            // Show the latest notification
            $latestNotification = $customer->notifications()->latest()->first();
            if ($latestNotification) {
                $this->info("Latest notification: {$latestNotification->title}");
                $this->info("Message: {$latestNotification->message}");
            }
        } else {
            $this->error('❌ No notification was created for order completion');
        }
        
        $this->info('\n--- Testing Status Change: Complete to Cancelled ---');
        
        $currentCount = $newCount;
        
        // Change status to cancelled
        $order->update([
            'order_status' => OrderStatus::CANCELLED,
            'cancellation_reason' => 'Test cancellation via command',
            'cancelled_at' => now(),
        ]);
        $this->info('✅ Order status changed to cancelled');
        
        // Check if notification was created
        $finalCount = $notificationService->getUnreadCount($customer);
        $this->info("Final unread notifications: {$finalCount}");
        
        if ($finalCount > $currentCount) {
            $this->info('✅ Notification created for order cancellation!');
            
            // Show the latest notification
            $latestNotification = $customer->notifications()->latest()->first();
            if ($latestNotification) {
                $this->info("Latest notification: {$latestNotification->title}");
                $this->info("Message: {$latestNotification->message}");
            }
        } else {
            $this->error('❌ No notification was created for order cancellation');
        }
        
        $this->info('\n--- Recent Notifications ---');
        $recentNotifications = $notificationService->getRecentNotifications($customer, 3);
        foreach ($recentNotifications as $notification) {
            $status = $notification->is_read ? '✓' : '●';
            $this->info("{$status} {$notification->title} - {$notification->created_at->diffForHumans()}");
        }
        
        $this->info('\n✅ Order status change notification test completed!');
    }
}
