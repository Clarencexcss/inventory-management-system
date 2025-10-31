<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Eager load orders relationship to prevent N+1 queries
        $customer = Auth::user()->load('orders');
        
        // Calculate statistics to avoid multiple database queries in the view
        $totalOrders = $customer->orders->count();
        $completedOrders = $customer->orders->filter(function ($order) {
            return $order->order_status === OrderStatus::COMPLETE;
        })->count();
        
        $pendingOrders = $customer->orders->filter(function ($order) {
            return $order->order_status === OrderStatus::PENDING;
        })->count();
        
        $totalSpent = $customer->orders->filter(function ($order) {
            return $order->order_status === OrderStatus::COMPLETE;
        })->sum('total');
        
        // Get recent notifications
        $notifications = $customer->notifications()->latest()->limit(4)->get();
        
        return view('customer.dashboard', compact(
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'totalSpent',
            'notifications'
        ));
    }
}