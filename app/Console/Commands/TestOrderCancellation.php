<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class TestOrderCancellation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-cancellation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test order cancellation and notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing order cancellation and notification system...');

        // Get a pending order
        $order = Order::with('customer')->where('order_status', 'pending')->first();

        if (!$order) {
            $this->error('No pending orders found. Please create some orders first.');
            return;
        }

        $this->info("Found pending order: #{$order->invoice_no} from {$order->customer->name}");

        // Get notification count before cancellation
        $notificationService = app(AdminNotificationService::class);
        $beforeCount = $notificationService->getUnreadCount();
        $this->info("Unread notifications before cancellation: {$beforeCount}");

        // Cancel the order
        $this->info('Cancelling the order...');
        $order->cancel('Test cancellation from command line', null);

        // Check notification count after cancellation
        $afterCount = $notificationService->getUnreadCount();
        $this->info("Unread notifications after cancellation: {$afterCount}");

        if ($afterCount > $beforeCount) {
            $this->info('✅ SUCCESS: Notification was created for order cancellation!');
            
            // Show the latest notification
            $latestNotification = AdminNotification::latest()->first();
            if ($latestNotification) {
                $this->info("Latest notification: {$latestNotification->title}");
                $this->info("Message: {$latestNotification->message}");
                $this->info("Type: {$latestNotification->type}");
            }
        } else {
            $this->error('❌ FAILED: No notification was created for order cancellation!');
        }

        $this->info('Test completed.');
    }
}
