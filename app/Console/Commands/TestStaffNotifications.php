<?php

namespace App\Console\Commands;

use App\Models\StaffNotification;
use App\Models\Order;
use App\Models\User;
use App\Services\StaffNotificationService;
use Illuminate\Console\Command;

class TestStaffNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:staff-notifications {--count=5 : Number of test notifications to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications for the staff notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->option('count');
        $notificationService = app(StaffNotificationService::class);

        $this->info("Creating {$count} test staff notifications...");

        // Get existing orders and users
        $orders = Order::with('customer')->take($count)->get();
        $users = User::where('role', 'staff')->take(2)->get();

        if ($orders->isEmpty()) {
            $this->error('No orders found. Please create some orders first.');
            return;
        }

        $created = 0;
        foreach ($orders as $order) {
            if ($created >= $count) break;
            
            // Create pending order notification
            $notificationService->createPendingOrderNotification($order);
            $this->info("Created pending order notification for order #{$order->invoice_no}");
            $created++;
        }

        // Create a cancelled order notification if we have users
        if ($users->isNotEmpty() && $orders->isNotEmpty()) {
            $order = $orders->first();
            $user = $users->first();
            
            $notificationService->createCancelledOrderNotification($order, $user);
            $this->info("Created cancelled order notification for order #{$order->invoice_no}");
            $created++;
        }

        $this->info("Successfully created {$created} test staff notifications!");
        
        // Show notification stats
        $total = StaffNotification::count();
        $unread = StaffNotification::unread()->count();
        
        $this->line("Total staff notifications: {$total}");
        $this->line("Unread staff notifications: {$unread}");
    }
}