<?php

namespace App\Livewire;

use App\Models\CustomerNotification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CustomerNotificationNavbar extends Component
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
        $customer = Auth::guard('web_customer')->user();
        
        if ($customer) {
            $this->unreadCount = $customer->notifications()->unread()->count();
            $this->notifications = $customer->notifications()
                ->latest()
                ->limit(5)
                ->get()
                ->toArray();
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        $customer = Auth::guard('web_customer')->user();
        $notification = CustomerNotification::find($notificationId);
        
        if ($notification && $notification->customer_id === $customer->id && $notification->isUnread()) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        $customer = Auth::guard('web_customer')->user();
        
        if ($customer) {
            $customer->notifications()->unread()->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            $this->loadNotifications();
        }
    }

    public function goToOrder($orderId)
    {
        return redirect()->route('customer.orders');
    }

    public function render()
    {
        return view('livewire.customer-notification-navbar');
    }
}
