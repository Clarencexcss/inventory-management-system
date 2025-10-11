<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => $this->notificationService->getUnreadCount()
        ]);
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications()
    {
        $notifications = $this->notificationService->getRecentNotifications(10);
        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(AdminNotification $notification)
    {
        $this->notificationService->markAsRead($notification);
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Get notification statistics
     */
    public function getStats()
    {
        return response()->json([
            'unread_count' => $this->notificationService->getUnreadCount(),
            'pending_orders_count' => $this->notificationService->getPendingOrdersCount(),
            'recent_cancelled_count' => $this->notificationService->getRecentCancelledOrdersCount(),
        ]);
    }
}
