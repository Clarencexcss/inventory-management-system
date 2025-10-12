<?php

namespace App\Livewire;

use App\Models\CustomerNotification;
use App\Services\CustomerNotificationService;
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
            $notificationService = app(CustomerNotificationService::class);
            $this->unreadCount = $notificationService->getUnreadCount($customer);
            $this->notifications = $notificationService->getRecentNotifications($customer, 5)->toArray();
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
            $notificationService = app(CustomerNotificationService::class);
            $notificationService->markAsRead($notification);
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        $customer = Auth::guard('web_customer')->user();
        
        if ($customer) {
            $notificationService = app(CustomerNotificationService::class);
            $notificationService->markAllAsRead($customer);
            $this->loadNotifications();
        }
    }

    public function goToOrder($orderId)
    {
        // Mark the notification as read when clicked
        $customer = Auth::guard('web_customer')->user();
        $notification = CustomerNotification::where('customer_id', $customer->id)
            ->whereJsonContains('data->order_id', $orderId)
            ->first();
            
        if ($notification && $notification->isUnread()) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
        
        return redirect()->route('customer.orders');
    }

    public function render()
    {
        return view('livewire.customer-notification-navbar');
    }
}
