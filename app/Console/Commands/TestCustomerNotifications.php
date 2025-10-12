<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Customer;
use App\Services\CustomerNotificationService;
use App\Enums\OrderStatus;

class TestCustomerNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:customer-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test customer notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Customer Notification System...');
        
        // Get or create a test customer
        $customer = Customer::first();
        if (!$customer) {
            $this->error('No customers found. Please create a customer first.');
            return;
        }
        
        $this->info("Using customer: {$customer->name} (ID: {$customer->id})");
        
        // Get or create a test order
        $order = Order::where('customer_id', $customer->id)->first();
        if (!$order) {
            $this->error('No orders found for this customer. Please create an order first.');
            return;
        }
        
        $this->info("Using order: #{$order->invoice_no} (ID: {$order->id})");
        
        $notificationService = app(CustomerNotificationService::class);
        
        // Test creating different types of notifications
        $this->info('\n--- Testing Notification Creation ---');
        
        // Test order placed notification
        $this->info('Creating order placed notification...');
        $placedNotification = $notificationService->createOrderPlacedNotification($order);
        $this->info("✅ Created: {$placedNotification->title}");
        
        // Test order completed notification
        $this->info('Creating order completed notification...');
        $completedNotification = $notificationService->createOrderCompletedNotification($order);
        $this->info("✅ Created: {$completedNotification->title}");
        
        // Test order cancelled notification
        $this->info('Creating order cancelled notification...');
        $cancelledNotification = $notificationService->createOrderCancelledNotification($order, 'Test cancellation');
        $this->info("✅ Created: {$cancelledNotification->title}");
        
        // Test order status update notification
        $this->info('Creating order status update notification...');
        $statusNotification = $notificationService->createOrderStatusUpdateNotification($order, 'pending', 'complete');
        $this->info("✅ Created: {$statusNotification->title}");
        
        // Test getting notifications
        $this->info('\n--- Testing Notification Retrieval ---');
        
        $unreadCount = $notificationService->getUnreadCount($customer);
        $this->info("Unread notifications count: {$unreadCount}");
        
        $recentNotifications = $notificationService->getRecentNotifications($customer, 5);
        $this->info("Recent notifications count: {$recentNotifications->count()}");
        
        $this->info('\n--- Recent Notifications ---');
        foreach ($recentNotifications as $notification) {
            $status = $notification->is_read ? '✓' : '●';
            $this->info("{$status} {$notification->title} - {$notification->created_at->diffForHumans()}");
        }
        
        // Test marking notifications as read
        $this->info('\n--- Testing Mark as Read ---');
        $unreadNotifications = $notificationService->getUnreadNotifications($customer);
        
        if ($unreadNotifications->count() > 0) {
            $firstUnread = $unreadNotifications->first();
            $this->info("Marking notification as read: {$firstUnread->title}");
            $notificationService->markAsRead($firstUnread);
            $this->info('✅ Marked as read');
        }
        
        // Get updated unread count
        $newUnreadCount = $notificationService->getUnreadCount($customer);
        $this->info("Updated unread count: {$newUnreadCount}");
        
        // Test getting statistics
        $this->info('\n--- Notification Statistics ---');
        $stats = $notificationService->getNotificationStatistics($customer);
        $this->info("Total: {$stats['total']}");
        $this->info("Unread: {$stats['unread']}");
        $this->info("Read: {$stats['read']}");
        $this->info("Recent: {$stats['recent']}");
        
        $this->info('\n✅ Customer notification system test completed successfully!');
    }
}
