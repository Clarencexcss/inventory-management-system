<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function getCustomerNotifications($userId)
    {
        return Notification::where('user_id', $userId)
                            ->latest()
                            ->get();
    }

    public function getAdminNotifications($userId)
    {
        return Notification::where('user_id', $userId)
                            ->latest()
                            ->get();
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update(['read' => true]);
    }

    public function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)->update(['read' => true]);
    }
}
