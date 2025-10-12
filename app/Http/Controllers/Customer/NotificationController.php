<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerNotification;
use App\Services\CustomerNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(CustomerNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display customer's notifications page
     */
    public function index(Request $request)
    {
        $customer = $request->user();
        $perPage = $request->get('per_page', 15);

        $notifications = $customer->notifications()
            ->latest()
            ->paginate($perPage);
            
        $unreadCount = $this->notificationService->getUnreadCount($customer);

        return view('customer.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Get customer's notifications (API)
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $customer = $request->user();
        $perPage = $request->get('per_page', 15);

        $notifications = $customer->notifications()
            ->latest()
            ->paginate($perPage);

        return response()->json($notifications);
    }

    /**
     * Get recent notifications (API)
     */
    public function recent(Request $request): JsonResponse
    {
        $customer = $request->user();
        $limit = $request->get('limit', 10);

        $notifications = $this->notificationService->getRecentNotifications($customer, $limit);

        return response()->json($notifications);
    }

    /**
     * Get unread notifications count (API)
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $customer = $request->user();
        $count = $this->notificationService->getUnreadCount($customer);

        return response()->json(['count' => $count]);
    }

    /**
     * Get notification statistics (API)
     */
    public function statistics(Request $request): JsonResponse
    {
        $customer = $request->user();
        $stats = $this->notificationService->getNotificationStatistics($customer);

        return response()->json($stats);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $customer = $request->user();
        $this->notificationService->markAllAsRead($customer);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Show specific notification (API)
     */
    public function show(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only view their own notifications
        if ($notification->customer_id !== $customer->id) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json($notification);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, CustomerNotification $notification)
    {
        $customer = $request->user();

        // Ensure customer can only update their own notifications
        if ($notification->customer_id !== $customer->id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Notification not found'], 404);
            }
            return back()->with('error', 'Notification not found.');
        }

        $this->notificationService->markAsRead($notification);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark notification as unread (API)
     */
    public function markAsUnread(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only update their own notifications
        if ($notification->customer_id !== $customer->id) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->markAsUnread();

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification (API)
     */
    public function destroy(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only delete their own notifications
        if ($notification->customer_id !== $customer->id) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }
}
