@extends('layouts.butcher')

@section('title', 'Staff Notifications')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-bell me-2"></i>Staff Notifications
            </h1>
        </div>
    </div>
    
    <x-alert/>
    
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-md-3 mb-4">
            <div class="card border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-bell text-primary fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['total'] }}</h5>
                            <p class="mb-0 text-muted small">Total Notifications</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-envelope-open-text text-warning fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['unread'] }}</h5>
                            <p class="mb-0 text-muted small">Unread</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-start border-4 border-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-clock text-info fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['pending_orders'] }}</h5>
                            <p class="mb-0 text-muted small">Pending Orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-start border-4 border-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-times-circle text-danger fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $stats['recent_cancelled'] }}</h5>
                            <p class="mb-0 text-muted small">Cancelled (7 days)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Staff Notifications
                    </h5>
                    <div>
                        @if($unreadCount > 0)
                            <form action="{{ route('staff.notifications.mark-all-read') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-check-double me-1"></i>Mark All Read ({{ $unreadCount }})
                                </button>
                            </form>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload();">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <div class="list-group-item notification-item {{ !$notification->is_read ? 'bg-light border-start border-primary border-4' : '' }}" style="transition: all 0.2s ease;">
                                <div class="d-flex align-items-start p-3">
                                    <div class="me-3 flex-shrink-0">
                                        @if($notification->type === 'pending_order')
                                            <span class="bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-clock fa-lg"></i>
                                            </span>
                                        @elseif($notification->type === 'cancelled_order')
                                            <span class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-times fa-lg"></i>
                                            </span>
                                        @elseif($notification->type === 'order_completed')
                                            <span class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-check fa-lg"></i>
                                            </span>
                                        @elseif($notification->type === 'low_stock')
                                            <span class="bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                                            </span>
                                        @else
                                            <span class="bg-info text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-bell fa-lg"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-1 {{ $notification->is_read ? 'text-muted' : 'fw-bold' }}">
                                                {{ $notification->title }}
                                            </h6>
                                            <small class="text-muted">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p class="mb-2 {{ $notification->is_read ? 'text-muted' : '' }}">
                                            {{ $notification->message }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary">New</span>
                                                @endif
                                                <span class="badge bg-secondary">{{ $notification->getTypeDisplayAttribute() }}</span>
                                            </div>
                                            <div>
                                                @if($notification->order)
                                                    <a href="{{ route('orders.show', $notification->order) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>View Order
                                                    </a>
                                                @endif
                                                @if(!$notification->is_read)
                                                    <form action="{{ route('staff.notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-check me-1"></i>Mark Read
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="m-0">
                            @endif
                        @endforeach
                        
                        <div class="card-footer">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No notifications yet</h4>
                            <p class="text-muted mb-4">System and order notifications will appear here</p>
                            <a href="{{ route('orders.index') }}" class="btn btn-primary">
                                <i class="fas fa-list me-1"></i>View Orders
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item:hover {
    background-color: #f8f9fa !important;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
</style>
@endsection