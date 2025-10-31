<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Order;

class DebugCustomerOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-customer-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug customer orders for dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Debugging customer orders...');
        
        // Check all customers
        $customers = Customer::all();
        $this->info("Total customers: " . $customers->count());
        
        foreach ($customers as $customer) {
            $this->line("Customer ID: {$customer->id}, Name: {$customer->name}, Email: {$customer->email}");
        }
        
        // Check all orders
        $orders = Order::all();
        $this->info("\nTotal orders: " . $orders->count());
        
        foreach ($orders as $order) {
            $status = $order->order_status instanceof \App\Enums\OrderStatus ? $order->order_status->value : $order->order_status;
            $this->line("Order ID: {$order->id}, Customer ID: {$order->customer_id}, Status: $status");
        }
        
        // Find a customer with orders
        $customerWithOrders = null;
        foreach ($customers as $customer) {
            $customerOrders = $customer->orders;
            if ($customerOrders->count() > 0) {
                $customerWithOrders = $customer;
                break;
            }
        }
        
        if ($customerWithOrders) {
            $this->info("\nCustomer with orders:");
            $this->info("Customer ID: {$customerWithOrders->id}");
            $this->info("Customer name: {$customerWithOrders->name}");
            $this->info("Customer orders count: {$customerWithOrders->orders->count()}");
            
            // Test the dashboard calculations
            $completedOrders = $customerWithOrders->orders->filter(function ($order) {
                return $order->order_status === \App\Enums\OrderStatus::COMPLETE;
            })->count();
            
            $pendingOrders = $customerWithOrders->orders->filter(function ($order) {
                return $order->order_status === \App\Enums\OrderStatus::PENDING;
            })->count();
            
            $totalSpent = $customerWithOrders->orders->filter(function ($order) {
                return $order->order_status === \App\Enums\OrderStatus::COMPLETE;
            })->sum('total');
            
            $this->info("\nDirect collection filtering (using enum comparison):");
            $this->info("Completed orders: $completedOrders");
            $this->info("Pending orders: $pendingOrders");
            $this->info("Total spent: $totalSpent");
            
            // Test relationship methods
            $completedOrdersViaRelationship = $customerWithOrders->completedOrders()->count();
            $pendingOrdersViaRelationship = $customerWithOrders->pendingOrders()->count();
            $totalSpentViaRelationship = $customerWithOrders->completedOrders()->sum('total');
            
            $this->info("\nVia relationship methods:");
            $this->info("Completed orders: $completedOrdersViaRelationship");
            $this->info("Pending orders: $pendingOrdersViaRelationship");
            $this->info("Total spent: $totalSpentViaRelationship");
        } else {
            $this->info("No customer with orders found.");
        }
    }
}