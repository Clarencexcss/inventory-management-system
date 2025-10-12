<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\Customer;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;

class TestOrderNotificationN1 extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:order-notification-n1';

    /**
     * The console command description.
     */
    protected $description = 'Test for N+1 queries when creating orders and notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Order notification creation for N+1 queries...');
        
        // Test creating a new order (should trigger AdminNotification creation)
        $this->testOrderCreation();
        
        // Test updating order status (should trigger CustomerNotification)
        $this->testOrderStatusUpdate();
        
        // Test manual order cancellation
        $this->testManualOrderCancellation();
        
        $this->info('All tests completed.');
        
        return Command::SUCCESS;
    }
    
    private function testOrderCreation()
    {
        $this->info('\n=== Testing Order Creation (Admin Notification) ===');
        
        DB::enableQueryLog();
        
        $customer = Customer::first();
        if (!$customer) {
            $this->error('No customers found. Please seed some data first.');
            return;
        }
        
        // Create a new pending order
        $order = Order::create([
            'customer_id' => $customer->id,
            'order_date' => now(),
            'order_status' => OrderStatus::PENDING,
            'total_products' => 1,
            'sub_total' => 100.00,
            'vat' => 12.00,
            'total' => 112.00,
            'invoice_no' => 'TEST-' . time(),
            'payment_type' => 'cash',
            'pay' => 0,
            'due' => 112.00,
        ]);
        
        $queries = DB::getQueryLog();
        $this->info('Queries executed for order creation: ' . count($queries));
        
        foreach ($queries as $index => $query) {
            $this->line(($index + 1) . '. ' . $query['query']);
        }
        
        // Clean up
        $order->delete();
        
        DB::flushQueryLog();
    }
    
    private function testOrderStatusUpdate()
    {
        $this->info('\n=== Testing Order Status Update (Customer Notification) ===');
        
        $order = Order::with('customer')->where('order_status', OrderStatus::PENDING)->first();
        if (!$order) {
            $this->error('No pending orders found.');
            return;
        }
        
        DB::enableQueryLog();
        
        // Update order status to complete
        $order->order_status = OrderStatus::COMPLETE;
        $order->save();
        
        $queries = DB::getQueryLog();
        $this->info('Queries executed for status update: ' . count($queries));
        
        foreach ($queries as $index => $query) {
            $this->line(($index + 1) . '. ' . $query['query']);
        }
        
        // Revert back
        $order->order_status = OrderStatus::PENDING;
        $order->save();
        
        DB::flushQueryLog();
    }
    
    private function testManualOrderCancellation()
    {
        $this->info('\n=== Testing Manual Order Cancellation ===');
        
        $order = Order::with('customer')->where('order_status', OrderStatus::PENDING)->first();
        if (!$order) {
            $this->error('No pending orders found.');
            return;
        }
        
        DB::enableQueryLog();
        
        // Use the cancel method
        $order->cancel('Test cancellation reason');
        
        $queries = DB::getQueryLog();
        $this->info('Queries executed for order cancellation: ' . count($queries));
        
        foreach ($queries as $index => $query) {
            $this->line(($index + 1) . '. ' . $query['query']);
        }
        
        // Revert back
        $order->order_status = OrderStatus::PENDING;
        $order->cancelled_at = null;
        $order->cancellation_reason = null;
        $order->save();
        
        DB::flushQueryLog();
    }
}