<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Customer Dashboard</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: #8B0000;
            --secondary-color: #4A0404;
            --accent-color: #FF4136;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
        }

        /* Navbar custom colors */
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #ffffff !important;
        }

        .navbar-nav .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            transition: all 0.2s;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: #ffd1d1 !important;
            transform: translateY(-1px);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .btn-equal {
            width: 100%;
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.25rem;
        }

        .stat-card {
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s, box-shadow 0.2s;
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

        .stat-card.info {
            border-left-color: #17a2b8;
        }

        .avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .notification-item {
            border-left: 3px solid #e9ecef;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }

        .notification-item.border-primary {
            border-left-color: var(--primary-color);
        }

        .page-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .dashboard-welcome {
            background: linear-gradient(135deg, var(--primary-color), #c00000);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .dashboard-welcome h3 {
            font-weight: 600;
        }

        .action-card {
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .action-card.store {
            border-left-color: #dc3545;
        }

        .action-card.orders {
            border-left-color: #17a2b8;
        }

        .action-card.profile {
            border-left-color: #6f42c1;
        }

        .badge-new {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-drumstick-bite me-2"></i>
                Yannis Meatshop - Customer Portal
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="{{ route('customer.products') }}">
                        <i class="fas fa-store me-1"></i>Products
                    </a>

                    <a class="nav-link" href="{{ route('customer.cart') }}">
                        <i class="fas fa-shopping-cart me-1"></i>Cart
                        @if(\Gloudemans\Shoppingcart\Facades\Cart::instance('customer')->count() > 0)
                            <span class="badge bg-danger ms-1">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('customer')->count() }}</span>
                        @endif
                    </a>

                    <!-- Livewire Notification Bell -->
                    @livewire('customer-notification-navbar')

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            {{ auth()->user()->name ?? 'Customer' }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                                    <i class="fas fa-home me-2"></i>Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                    <i class="fas fa-user-edit me-2"></i>My Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="dashboard-welcome p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">
                                <i class="fas fa-hand-wave me-2"></i>Welcome back, {{ auth()->user()->name ?? 'Customer' }}!
                            </h3>
                            <p class="mb-0 opacity-75">Here's what's happening with your account today.</p>
                        </div>
                        <div class="text-end">
                            <div class="small opacity-75">{{ now()->format('l, F j, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Section -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="bg-primary text-white avatar me-3">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                            <div>
                                <div class="h4 mb-0 text-primary">
                                    {{ $totalOrders }}
                                </div>
                                <div class="text-muted small">Total Orders</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card stat-card success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="bg-success text-white avatar me-3">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <div>
                                <div class="h4 mb-0 text-success">
                                    {{ $completedOrders }}
                                </div>
                                <div class="text-muted small">Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card stat-card warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="bg-warning text-white avatar me-3">
                                <i class="fas fa-clock"></i>
                            </span>
                            <div>
                                <div class="h4 mb-0 text-warning">
                                    {{ $pendingOrders }}
                                </div>
                                <div class="text-muted small">Pending</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card stat-card info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="bg-info text-white avatar me-3">
                                <i class="fas fa-wallet"></i>
                            </span>
                            <div>
                                <div class="h4 mb-0 text-info">
                                    ₱{{ number_format($totalSpent, 2) }}
                                </div>
                                <div class="text-muted small">Total Spent</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bell me-2"></i>Recent Notifications
                        </h5>
                        <a href="{{ route('customer.notifications.index') }}" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-eye me-1"></i>View All
                        </a>
                    </div>
                    <div class="card-body">
                        @if($notifications->count() > 0)
                            <div class="row">
                                @foreach($notifications as $notification)
                                    <div class="col-md-6 mb-3">
                                        <div class="notification-item 
                                            @if(!$notification->is_read) border-primary @endif
                                        ">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3 flex-shrink-0">
                                                    @if($notification->type === 'order_completed')
                                                        <span class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                            <i class="fas fa-check"></i>
                                                        </span>
                                                    @elseif($notification->type === 'order_cancelled')
                                                        <span class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                            <i class="fas fa-times"></i>
                                                        </span>
                                                    @elseif($notification->type === 'order_placed')
                                                        <span class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </span>
                                                    @else
                                                        <span class="bg-secondary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                            <i class="fas fa-bell"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : 'text-muted' }}">
                                                                {{ $notification->title }}
                                                            </h6>
                                                            <p class="mb-1 small text-muted">
                                                                {{ Str::limit($notification->message, 60) }}
                                                            </p>
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock me-1"></i>
                                                                {{ $notification->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                        @if(!$notification->is_read)
                                                            <span class="badge bg-primary badge-new">New</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No notifications yet</h5>
                                <p class="text-muted">We'll notify you when there are updates to your orders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card action-card store h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-store me-2"></i>Browse Products
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="card-text">Explore our fresh meat products and add them to your cart.</p>
                        <div class="mt-auto">
                            <a href="{{ route('customer.products') }}" class="btn btn-danger btn-equal">
                                <i class="fas fa-shopping-bag me-1"></i>Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card action-card orders h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>My Orders
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="card-text">View and track your order history and status.</p>
                        <div class="mt-auto">
                            <a href="{{ route('customer.orders') }}" class="btn btn-info btn-equal">
                                <i class="fas fa-eye me-1"></i>View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card action-card profile h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="card-text">Update your personal information and preferences.</p>
                        <div class="mt-auto">
                            <a href="{{ route('customer.profile') }}" class="btn btn-purple btn-equal">
                                <i class="fas fa-edit me-1"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    
    <script>
        // Add custom purple color for the profile button
        const style = document.createElement('style');
        style.innerHTML = `
            .btn-purple {
                background-color: #6f42c1;
                border-color: #6f42c1;
                color: white;
                font-weight: 500;
                padding: 0.5rem 1.25rem;
                border-radius: 6px;
                transition: all 0.3s;
                width: 100%;
                min-height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .btn-purple:hover {
                background-color: #5a32a3;
                border-color: #5a32a3;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                color: white;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>