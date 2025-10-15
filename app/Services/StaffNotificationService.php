<?php

namespace App\Services;

use App\Models\StaffNotification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StaffNotificationService
{
    /**
     * Create a pending order notification
     */
    public function createPendingOrderNotification(Order $order): StaffNotification
    {
        // Ensure customer relationship is loaded
        if (!$order->relationLoaded('customer')) {
            $order->load('customer');
        }
        
        $notification = StaffNotification::create([
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
        ]);
        
        // Clear notification cache
        $this->clearNotificationCache();
        
        return $notification;
    }

    /**
     * Create a cancelled order notification
     */
    public function createCancelledOrderNotification(Order $order, ?User $cancelledBy = null): StaffNotification
    {
        // Ensure customer relationship is loaded
        if (!$order->relationLoaded('customer')) {
            $order->load('customer');
        }
        
        $cancelledByName = $cancelledBy ? $cancelledBy->name : 'Customer';
        
        $notification = StaffNotification::create([
            'type' => 'cancelled_order',
            'title' => 'Order Cancelled',
            'message' => "Order #{$order->invoice_no} from {$order->customer->name} has been cancelled by {$cancelledByName}.",
            'data' => [
                'order_id' => $order->id,
                'customer_name' => $order->customer->name,
                'cancelled_by' => $cancelledByName,
                'cancellation_reason' => $order->cancellation_reason,
                'cancelled_at' => $order->cancelled_at?->format('Y-m-d H:i:s'),
            ],
            'order_id' => $order->id,
            'cancelled_by_user_id' => $cancelledBy?->id,
        ]);
        
        // Clear notification cache
        $this->clearNotificationCache();
        
        return $notification;
    }

    /**
     * Create a low stock notification
     */
    public function createLowStockNotification($product): StaffNotification
    {
        $notification = StaffNotification::create([
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => "Product {$product->name} (Code: {$product->product_code}) is running low. Current stock: {$product->quantity} {$product->unit->name}.",
            'data' => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->product_code,
                'current_stock' => $product->quantity,
                'unit' => $product->unit->name,
            ],
        ]);
        
        // Clear notification cache
        $this->clearNotificationCache();
        
        return $notification;
    }

    /**
     * Get unread notifications count with caching
     */
    public function getUnreadCount(): int
    {
        $cacheKey = 'staff_unread_notifications_count_' . auth()->id();
        return Cache::remember($cacheKey, 30, function() {
            return StaffNotification::unread()->count();
        });
    }

    /**
     * Get recent notifications with caching
     */
    public function getRecentNotifications(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = 'staff_recent_notifications_' . auth()->id() . '_' . $limit;
        return Cache::remember($cacheKey, 30, function() use ($limit) {
            return StaffNotification::with(['order' => function($query) {
                    $query->with('customer');
                }, 'cancelledByUser'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get unread notifications with caching
     */
    public function getUnreadNotifications(): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = 'staff_unread_notifications_' . auth()->id();
        return Cache::remember($cacheKey, 30, function() {
            return StaffNotification::with(['order' => function($query) {
                    $query->with('customer');
                }, 'cancelledByUser'])
                ->unread()
                ->latest()
                ->get();
        });
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(StaffNotification $notification): void
    {
        $notification->markAsRead();
        $this->clearNotificationCache();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): void
    {
        StaffNotification::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        $this->clearNotificationCache();
    }

    /**
     * Clear notification cache
     */
    private function clearNotificationCache(): void
    {
        $userId = auth()->id();
        Cache::forget('staff_unread_notifications_count_' . $userId);
        Cache::forget('staff_recent_notifications_' . $userId . '_5');
        Cache::forget('staff_recent_notifications_' . $userId . '_10');
        Cache::forget('staff_unread_notifications_' . $userId);
    }

    /**
     * Get pending orders count
     */
    public function getPendingOrdersCount(): int
    {
        return Order::pending()->count();
    }

    /**
     * Get recent cancelled orders count
     */
    public function getRecentCancelledOrdersCount(int $days = 7): int
    {
        return Order::cancelled()
            ->where('cancelled_at', '>=', now()->subDays($days))
            ->count();
    }
}