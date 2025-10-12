<?php

namespace App\Console\Commands;

use App\Services\AdminNotificationService;
use App\Models\AdminNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestN1Queries extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:n1-queries';

    /**
     * The console command description.
     */
    protected $description = 'Test for N+1 queries in admin notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing AdminNotificationService for N+1 queries...');
        
        // Test 1: AdminNotificationService methods
        $this->testAdminNotificationService();
        
        // Test 2: Direct AdminNotification queries
        $this->testDirectQueries();
        
        // Test 3: Controller pagination query
        $this->testPaginationQuery();
        
        $this->info('Test completed.');
        
        return Command::SUCCESS;
    }
    
    private function testAdminNotificationService()
    {
        $this->info('\n=== Testing AdminNotificationService ===');
        
        DB::enableQueryLog();
        
        $service = app(AdminNotificationService::class);
        
        $this->info('Testing getRecentNotifications...');
        $notifications = $service->getRecentNotifications(5);
        
        $queries = DB::getQueryLog();
        $this->info('Queries executed: ' . count($queries));
        
        foreach ($queries as $index => $query) {
            $this->line(($index + 1) . '. ' . $query['query']);
        }
        
        DB::flushQueryLog();
    }
    
    private function testDirectQueries()
    {
        $this->info('\n=== Testing Direct AdminNotification Queries ===');
        
        DB::enableQueryLog();
        
        // Test the exact query from controller
        $notifications = AdminNotification::with(['order.customer', 'cancelledByUser'])
            ->latest()
            ->take(5)
            ->get();
            
        // Simulate accessing relationships like in the view
        foreach ($notifications as $notification) {
            if ($notification->order) {
                $customerName = $notification->order->customer->name ?? 'N/A';
                $total = $notification->order->total ?? 0;
            }
        }
        
        $queries = DB::getQueryLog();
        $this->info('Queries executed: ' . count($queries));
        
        foreach ($queries as $index => $query) {
            $this->line(($index + 1) . '. ' . $query['query']);
        }
        
        DB::flushQueryLog();
    }
    
    private function testPaginationQuery()
    {
        $this->info('\n=== Testing Pagination Query (like in controller) ===');
        
        DB::enableQueryLog();
        
        $notifications = AdminNotification::with(['order.customer', 'cancelledByUser'])
            ->latest()
            ->paginate(15);
            
        // Access the items to trigger lazy loading if any
        foreach ($notifications->take(3) as $notification) {
            if ($notification->order) {
                $customerName = $notification->order->customer->name ?? 'N/A';
                if ($notification->cancelledByUser) {
                    $cancelledBy = $notification->cancelledByUser->name;
                }
            }
        }
        
        $queries = DB::getQueryLog();
        $this->info('Queries executed: ' . count($queries));
        
        foreach ($queries as $index => $query) {
            $this->line(($index + 1) . '. ' . $query['query']);
        }
        
        DB::flushQueryLog();
    }
}