<?php

namespace App\Services;

use App\Models\CustomerNotification;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class CustomerNotificationService
{
    /**
     * Create order placed notification
     */
    public function createOrderPlacedNotification(Order $order): CustomerNotification
    {
        return CustomerNotification::create([
            'customer_id' => $order->customer_id,
            'type' => 'order_placed',
            'title' => 'Order Placed Successfully',
            'message' => "Your order #{$order->invoice_no} has been placed successfully. We'll notify you once it's ready.",
            'data' => [
                'order_id' => $order->id,
                'invoice_no' => $order->invoice_no,
                'status' => 'placed',
                'total_amount' => $order->total,
                'order_date' => $order->order_date->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Create order completed notification
     */
    public function createOrderCompletedNotification(Order $order): CustomerNotification
    {
        return CustomerNotification::create([
            'customer_id' => $order->customer_id,
            'type' => 'order_completed',
            'title' => 'Order Completed',
            'message' => "Your order #{$order->invoice_no} has been completed and is ready for pickup/delivery.",
            'data' => [
                'order_id' => $order->id,
                'invoice_no' => $order->invoice_no,
                'status' => 'completed',
                'total_amount' => $order->total,
                'completed_at' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Create order cancelled notification
     */
    public function createOrderCancelledNotification(Order $order, string $reason = null): CustomerNotification
    {
        $cancelReason = $reason ?? $order->cancellation_reason ?? 'No reason provided';
        
        return CustomerNotification::create([
            'customer_id' => $order->customer_id,
            'type' => 'order_cancelled',
            'title' => 'Order Cancelled',
            'message' => "Your order #{$order->invoice_no} has been cancelled. Reason: {$cancelReason}",
            'data' => [
                'order_id' => $order->id,
                'invoice_no' => $order->invoice_no,
                'status' => 'cancelled',
                'cancellation_reason' => $cancelReason,
                'cancelled_at' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Create order status update notification
     */
    public function createOrderStatusUpdateNotification(Order $order, string $oldStatus, string $newStatus): CustomerNotification
    {
        $statusMessages = [
            'pending' => 'Your order is pending approval.',
            'complete' => 'Your order has been completed and is ready for pickup/delivery.',
            'cancelled' => 'Your order has been cancelled.',
        ];

        $message = $statusMessages[$newStatus] ?? "Your order status has been updated to {$newStatus}.";

        return CustomerNotification::create([
            'customer_id' => $order->customer_id,
            'type' => 'order_status_update',
            'title' => 'Order Status Updated',
            'message' => "Order #{$order->invoice_no}: {$message}",
            'data' => [
                'order_id' => $order->id,
                'invoice_no' => $order->invoice_no,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Get customer notifications
     */
    public function getCustomerNotifications(Customer $customer, int $limit = null): Collection
    {
        $query = $customer->notifications()->latest();
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(Customer $customer): int
    {
        return $customer->notifications()->unread()->count();
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications(Customer $customer, int $limit = 10): Collection
    {
        return $customer->notifications()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notifications
     */
    public function getUnreadNotifications(Customer $customer): Collection
    {
        return $customer->notifications()
            ->unread()
            ->latest()
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(CustomerNotification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for customer
     */
    public function markAllAsRead(Customer $customer): void
    {
        $customer->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get notification statistics for customer
     */
    public function getNotificationStatistics(Customer $customer): array
    {
        return [
            'total' => $customer->notifications()->count(),
            'unread' => $customer->notifications()->unread()->count(),
            'read' => $customer->notifications()->read()->count(),
            'recent' => $customer->notifications()->recent()->count(),
        ];
    }

    /**
     * Delete old notifications (older than specified days)
     */
    public function deleteOldNotifications(Customer $customer, int $days = 90): int
    {
        return $customer->notifications()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Get notifications by type
     */
    public function getNotificationsByType(Customer $customer, string $type): Collection
    {
        return $customer->notifications()
            ->where('type', $type)
            ->latest()
            ->get();
    }
}