@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-2"></i>Dashboard Overview
            </h1>
        </div>
    </div>

    <!-- Meat Inventory Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-drumstick-bite me-2"></i>Meat Inventory Overview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="bg-danger text-white p-3 rounded">
                                                <i class="fas fa-meat fa-2x"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3 class="mb-0">{{ $totalMeatCuts }}</h3>
                                            <div class="text-muted">Total Meat Cuts</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="bg-success text-white p-3 rounded">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3 class="mb-0">{{ $availableMeatCuts }}</h3>
                                            <div class="text-muted">Available Cuts</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="bg-warning text-white p-3 rounded">
                                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3 class="mb-0">{{ $lowStockMeatCuts }}</h3>
                                            <div class="text-muted">Low Stock Items</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="bg-info text-white p-3 rounded">
                                                <i class="fas fa-shopping-cart fa-2x"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3 class="mb-0">{{ $todayOrders }}</h3>
                                            <div class="text-muted">Today's Orders</div>
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
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>Recent Notifications
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="badge bg-warning ms-2">{{ $unreadNotifications }} unread</span>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($notifications->take(4) as $notification)
                        <div class="col-sm-6 col-lg-3 mb-3">
                            <div class="card border-start border-3 
                                @if($notification->type === 'pending_order') border-info
                                @elseif($notification->type === 'cancelled_order') border-danger
                                @elseif($notification->type === 'order_completed') border-success
                                @elseif($notification->type === 'low_stock') border-warning
                                @else border-secondary
                                @endif
                            ">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-2">
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
                                            <h6 class="mb-1 small {{ $notification->read_at ? 'text-muted' : 'fw-bold' }}">
                                                {{ $notification->title }}
                                            </h6>
                                            <p class="mb-1 small text-muted">
                                                {{ Str::limit($notification->message, 50) }}
                                            </p>
                                            <small class="text-muted">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
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
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Meat by Animal Type
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
                                    <td>{{ ucfirst($type) }}</td>
                                    <td>{{ $count }}</td>
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
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks me-2"></i>Orders Overview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>Today's Orders</div>
                        <div class="badge bg-info">{{ $todayOrders }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Completed Orders</div>
                        <div class="badge bg-success">{{ $completedOrders }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Total Orders</div>
                        <div class="badge bg-primary">{{ $orders }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .bg-danger {
        background-color: var(--primary-color) !important;
    }
</style>
@endpush
