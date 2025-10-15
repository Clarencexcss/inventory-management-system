<?php

namespace App\Livewire;

use App\Models\StaffNotification;
use App\Services\StaffNotificationService;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class StaffNotificationNavbar extends Component
{
    public $unreadCount = 0;
    public $notifications = [];
    public $showDropdown = false;

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        // Cache notification data for 30 seconds to reduce database queries
        $cacheKey = 'staff_notifications_' . auth()->id();
        $data = Cache::remember($cacheKey, 30, function() {
            $service = app(StaffNotificationService::class);
            return [
                'unreadCount' => $service->getUnreadCount(),
                'notifications' => $service->getRecentNotifications(5)->toArray()
            ];
        });
        
        $this->unreadCount = $data['unreadCount'];
        $this->notifications = $data['notifications'];
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        $notification = StaffNotification::find($notificationId);
        if ($notification && $notification->isUnread()) {
            $service = app(StaffNotificationService::class);
            $service->markAsRead($notification);
            
            // Clear cache to force refresh
            $cacheKey = 'staff_notifications_' . auth()->id();
            Cache::forget($cacheKey);
            
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        $service = app(StaffNotificationService::class);
        $service->markAllAsRead();
        
        // Clear cache to force refresh
        $cacheKey = 'staff_notifications_' . auth()->id();
        Cache::forget($cacheKey);
        
        $this->loadNotifications();
    }

    public function goToOrder($orderId)
    {
        // Mark the notification as read when clicked
        $notification = StaffNotification::where('order_id', $orderId)
            ->whereIn('type', ['pending_order', 'cancelled_order'])
            ->where('is_read', false)
            ->first();
            
        if ($notification) {
            $notification->markAsRead();
            
            // Clear cache to force refresh
            $cacheKey = 'staff_notifications_' . auth()->id();
            Cache::forget($cacheKey);
            
            $this->loadNotifications();
        }
        
        return redirect()->route('orders.show', $orderId);
    }

    public function render()
    {
        return view('livewire.staff-notification-navbar');
    }
}