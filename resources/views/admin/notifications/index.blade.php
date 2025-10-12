@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <x-back-button url="{{ route('dashboard') }}" text="Back to Dashboard" />
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Notifications</div>
                            <div class="h5 mb-0">{{ $stats['total'] }}</div>
                        </div>
                        <div class="fa-stack fa-2x text-white-25">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fas fa-bell fa-stack-1x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Unread</div>
                            <div class="h5 mb-0">{{ $stats['unread'] }}</div>
                        </div>
                        <div class="fa-stack fa-2x text-white-25">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fas fa-exclamation fa-stack-1x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Pending Orders</div>
                            <div class="h5 mb-0">{{ $stats['pending_orders'] }}</div>
                        </div>
                        <div class="fa-stack fa-2x text-white-25">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fas fa-clock fa-stack-1x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Recent Cancelled</div>
                            <div class="h5 mb-0">{{ $stats['recent_cancelled'] }}</div>
                        </div>
                        <div class="fa-stack fa-2x text-white-25">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fas fa-times fa-stack-1x text-white"></i>
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
                        Admin Notifications
                    </h5>
                    <div>
                        @if($unreadCount > 0)
                            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
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
                                        @else
                                            <span class="bg-info text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-bell fa-lg"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 fw-bold">{{ $notification->title }}</h6>
                                                <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                                
                                                @if($notification->order)
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            <strong>Customer:</strong> {{ $notification->order->customer->name ?? 'N/A' }} |
                                                            <strong>Amount:</strong> ₱{{ number_format($notification->order->total ?? 0, 2) }}
                                                        </small>
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $notification->created_at->format('M d, Y \\a\\t H:i A') }}
                                                    <span class="mx-2">•</span>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column align-items-end">
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary mb-2">New</span>
                                                @endif
                                                <div class="btn-group">
                                                    @if($notification->order_id)
                                                        <a href="{{ route('orders.show', $notification->order_id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i>View Order
                                                        </a>
                                                    @endif
                                                    @if(!$notification->is_read)
                                                        <form action="{{ route('admin.notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
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