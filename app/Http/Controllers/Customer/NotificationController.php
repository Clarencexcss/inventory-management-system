<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get customer's notifications
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $perPage = $request->get('per_page', 15);

        $notifications = $customer->notifications()
            ->latest()
            ->paginate($perPage);

        return response()->json($notifications);
    }

    /**
     * Get recent notifications
     */
    public function recent(Request $request): JsonResponse
    {
        $customer = $request->user();
        $limit = $request->get('limit', 10);

        $notifications = $customer->notifications()
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $customer = $request->user();
        $count = $customer->notifications()->unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get notification statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $customer = $request->user();
        
        $stats = [
            'total' => $customer->notifications()->count(),
            'unread' => $customer->notifications()->unread()->count(),
            'read' => $customer->notifications()->read()->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $customer = $request->user();
        $customer->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Show specific notification
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
    public function markAsRead(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only update their own notifications
        if ($notification->customer_id !== $customer->id) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark notification as unread
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
     * Delete notification
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
