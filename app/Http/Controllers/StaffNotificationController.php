<?php

namespace App\Http\Controllers;

use App\Models\StaffNotification;
use App\Services\StaffNotificationService;
use Illuminate\Http\Request;

class StaffNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(StaffNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the staff notifications page
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        $notifications = StaffNotification::with(['order' => function($query) {
                $query->with('customer');
            }, 'cancelledByUser'])
            ->latest()
            ->paginate($perPage);
            
        $unreadCount = $this->notificationService->getUnreadCount();
        $stats = [
            'total' => StaffNotification::count(),
            'unread' => $unreadCount,
            'pending_orders' => $this->notificationService->getPendingOrdersCount(),
            'recent_cancelled' => $this->notificationService->getRecentCancelledOrdersCount(),
        ];

        return view('staff.notifications.index', compact('notifications', 'unreadCount', 'stats'));
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
    public function markAsRead(Request $request, StaffNotification $staff_notification)
    {
        // Ensure the notification is loaded with relationships to avoid N+1 queries
        if (!$staff_notification->relationLoaded('order')) {
            $staff_notification->load(['order' => function($query) {
                $query->with('customer');
            }, 'cancelledByUser']);
        }
        
        $this->notificationService->markAsRead($staff_notification);
        
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