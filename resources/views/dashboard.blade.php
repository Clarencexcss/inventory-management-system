@extends('layouts.butcher')

@push('page-styles')
<style>
    .stat-card {
        border-left: 4px solid var(--primary-color);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 1.5rem;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .stat-card.success {
        border-left-color: #28a745;
    }
    .stat-card.warning {
        border-left-color: #ffc107;
    }
    .stat-card.danger {
        border-left-color: #dc3545;
    }
    .stat-card.info {
        border-left-color: #17a2b8;
    }
    .stat-card.dark {
        border-left-color: #343a40;
    }
    
    .avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    .notification-card {
        border-left: 3px solid #e9ecef;
        padding-left: 1rem;
        margin-bottom: 1rem;
    }
    
    .notification-card.border-info { border-left-color: #17a2b8; }
    .notification-card.border-danger { border-left-color: #dc3545; }
    .notification-card.border-success { border-left-color: #28a745; }
    .notification-card.border-warning { border-left-color: #ffc107; }
    
    .page-title {
        font-weight: 600;
        color: var(--primary-color);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-2"></i>Dashboard Overview
            </h1>
            <p class="text-muted">Welcome back! Here's what's happening with your business today.</p>
        </div>
    </div>

    <!-- Inventory Overview Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-box-open me-2 text-primary"></i>Inventory Overview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="bg-primary text-white avatar me-3">
                                            <i class="fas fa-box"></i>
                                        </span>
                                        <div>
                                            <div class="h3 mb-0">{{ $products }}</div>
                                            <div class="text-muted small">Total Products</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card stat-card success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="bg-success text-white avatar me-3">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div>
                                            <div class="h3 mb-0 text-success">{{ $availableProducts }}</div>
                                            <div class="text-muted small">Available</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card stat-card warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="bg-warning text-white avatar me-3">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                        <div>
                                            <div class="h3 mb-0 text-warning">{{ $lowStockProducts }}</div>
                                            <div class="text-muted small">Low Stock</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card stat-card danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="bg-danger text-white avatar me-3">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                        <div>
                                            <div class="h3 mb-0 text-danger">{{ $outOfStockProducts }}</div>
                                            <div class="text-muted small">Out of Stock</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card stat-card info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="bg-info text-white avatar me-3">
                                            <i class="fas fa-shopping-cart"></i>
                                        </span>
                                        <div>
                                            <div class="h3 mb-0 text-info">{{ $todayOrders }}</div>
                                            <div class="text-muted small">Today's Orders</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Section -->
    @if(isset($notifications) && $notifications->isNotEmpty())
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-bell me-2 text-primary"></i>Recent Notifications
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="badge bg-warning ms-2">{{ $unreadNotifications }} unread</span>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($notifications->take(4) as $notification)
                        <div class="col-md-6 mb-3">
                            <div class="notification-card 
                                @if($notification->type === 'pending_order') border-info
                                @elseif($notification->type === 'cancelled_order') border-danger
                                @elseif($notification->type === 'order_completed') border-success
                                @elseif($notification->type === 'low_stock') border-warning
                                @else border-secondary
                                @endif
                            ">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3 mt-1">
                                        @if($notification->type === 'pending_order')
                                            <i class="fas fa-clock text-info"></i>
                                        @elseif($notification->type === 'cancelled_order')
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @elseif($notification->type === 'order_completed')
                                            <i class="fas fa-check-circle text-success"></i>
                                        @elseif($notification->type === 'low_stock')
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                        @else
                                            <i class="fas fa-info-circle text-secondary"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 {{ $notification->read_at ? 'text-muted' : 'fw-bold' }}">
                                            {{ $notification->title }}
                                        </h6>
                                        <p class="mb-1 small text-muted">
                                            {{ Str::limit($notification->message, 80) }}
                                        </p>
                                        <small class="text-muted">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View All Notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Business Overview Section -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>Meat by Animal Type
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Animal Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($meatByAnimalType as $type => $count)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark me-2">
                                            {{ strtoupper(substr($type, 0, 2)) }}
                                        </span>
                                        {{ ucfirst($type) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-tasks me-2 text-primary"></i>Orders Overview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <div>
                            <i class="fas fa-calendar-day text-info me-2"></i>
                            Today's Orders
                        </div>
                        <div class="badge bg-info fs-6">{{ $todayOrders }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <div>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Completed Orders
                        </div>
                        <div class="badge bg-success fs-6">{{ $completedOrders }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <div>
                            <i class="fas fa-shopping-cart text-primary me-2"></i>
                            Total Orders
                        </div>
                        <div class="badge bg-primary fs-6">{{ $orders }}</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-clock text-warning me-2"></i>
                            Pending Orders
                        </div>
                        <div class="badge bg-warning fs-6">{{ $pendingOrders }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection