<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--count=5 : Number of test notifications to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications for the admin notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->option('count');
        $notificationService = app(AdminNotificationService::class);

        $this->info("Creating {$count} test notifications...");

        // Get existing orders and users
        $orders = Order::with('customer')->take($count)->get();
        $users = User::take(2)->get();

        if ($orders->isEmpty()) {
            $this->error('No orders found. Please create some orders first.');
            return;
        }

        $created = 0;
        foreach ($orders as $order) {
            if ($created >= $count) break;

            // Create pending order notification
            $notificationService->createPendingOrderNotification($order);
            $created++;
            $this->line("Created pending order notification for order #{$order->invoice_no}");

            if ($created >= $count) break;

            // Create cancelled order notification if we have users
            if ($users->isNotEmpty()) {
                $cancelledBy = $users->random();
                $notificationService->createCancelledOrderNotification($order, $cancelledBy);
                $created++;
                $this->line("Created cancelled order notification for order #{$order->invoice_no}");
            }
        }

        $this->info("Successfully created {$created} test notifications!");
        $this->info("Unread notifications count: " . $notificationService->getUnreadCount());
    }
}
