<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Enums\OrderStatus;

class DebugOrderUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:order-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug order update events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Debugging Order Update Events...');
        
        // Get the test order
        $order = Order::latest()->first();
        if (!$order) {
            $this->error('No orders found.');
            return;
        }
        
        $this->info("Using order: #{$order->invoice_no} (ID: {$order->id})");
        $this->info("Current status: {$order->order_status->value}");
        
        // Reset to pending
        $this->info('\nSetting order to pending...');
        $order->order_status = OrderStatus::PENDING;
        $order->save();
        $this->info("Order status: {$order->fresh()->order_status->value}");
        
        // Update to complete
        $this->info('\nUpdating order to complete...');
        $originalValue = $order->getOriginal('order_status');
        $this->info('Before update - getOriginal: ' . ($originalValue instanceof OrderStatus ? $originalValue->value : ($originalValue ?? 'null')));
        
        $order->order_status = OrderStatus::COMPLETE;
        $this->info('After setting status but before save: ' . $order->order_status->value);
        
        $order->save();
        $this->info('After save: ' . $order->fresh()->order_status->value);
        
        $this->info('\nDebug completed.');
    }
}
