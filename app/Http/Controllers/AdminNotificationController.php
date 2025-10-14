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
     * Display the admin notifications page
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        $notifications = AdminNotification::with(['order' => function($query) {
                $query->with('customer');
            }, 'cancelledByUser'])
            ->latest()
            ->paginate($perPage);
            
        $unreadCount = $this->notificationService->getUnreadCount();
        $stats = [
            'total' => AdminNotification::count(),
            'unread' => $unreadCount,
            'pending_orders' => $this->notificationService->getPendingOrdersCount(),
            'recent_cancelled' => $this->notificationService->getRecentCancelledOrdersCount(),
        ];

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'stats'));
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
        // Ensure the notification is loaded with relationships to avoid N+1 queries
        if (!$notification->relationLoaded('order')) {
            $notification->load(['order' => function($query) {
                $query->with('customer');
            }, 'cancelledByUser']);
        }
        
        $this->notificationService->markAsRead($notification);
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'All notifications marked as read.');
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
