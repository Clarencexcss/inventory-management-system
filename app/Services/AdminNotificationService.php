<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminNotificationService
{
    /**
     * Create a pending order notification
     */
    public function createPendingOrderNotification(Order $order): AdminNotification
    {
        return AdminNotification::create([
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
    }

    /**
     * Create a cancelled order notification
     */
    public function createCancelledOrderNotification(Order $order, ?User $cancelledBy = null): AdminNotification
    {
        $cancelledByName = $cancelledBy ? $cancelledBy->name : 'Customer';
        
        return AdminNotification::create([
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
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(): int
    {
        return AdminNotification::unread()->count();
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return AdminNotification::with(['order.customer', 'cancelledByUser'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notifications
     */
    public function getUnreadNotifications(): \Illuminate\Database\Eloquent\Collection
    {
        return AdminNotification::with(['order.customer', 'cancelledByUser'])
            ->unread()
            ->latest()
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(AdminNotification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): void
    {
        AdminNotification::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
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
