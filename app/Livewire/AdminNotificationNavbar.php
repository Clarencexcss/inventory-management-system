<?php

namespace App\Livewire;

use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Livewire\Component;

class AdminNotificationNavbar extends Component
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
        $service = app(AdminNotificationService::class);
        $this->unreadCount = $service->getUnreadCount();
        $this->notifications = $service->getRecentNotifications(5)->toArray();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        $notification = AdminNotification::find($notificationId);
        if ($notification && $notification->isUnread()) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        $service = app(AdminNotificationService::class);
        $service->markAllAsRead();
        $this->loadNotifications();
    }

    public function goToOrder($orderId)
    {
        return redirect()->route('orders.show', $orderId);
    }

    public function render()
    {
        return view('livewire.admin-notification-navbar');
    }
}
