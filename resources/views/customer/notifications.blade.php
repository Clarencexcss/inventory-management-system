<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - My Notifications</title>

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

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }
        
        .notification-item {
            transition: all 0.2s ease;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-drumstick-bite me-2"></i>
                Yannis Meatshop - Customer Portal
            </a>

            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('customer.products') }}">
                    <i class="fas fa-store me-1"></i>Products
                </a>
                <a class="nav-link" href="{{ route('customer.cart') }}">
                    <i class="fas fa-shopping-cart me-1"></i>Cart
                    <span class="badge bg-danger ms-1">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('customer')->count() }}</span>
                </a>
                <a class="nav-link" href="{{ route('customer.orders') }}">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>
                
                <!-- Livewire Notification Bell -->
                @livewire('customer-notification-navbar')

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name ?? 'Customer' }}
                    </a>
                    
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </a></li>
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
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="mb-3">
            <x-back-button url="{{ route('customer.dashboard') }}" text="Back to Dashboard" />
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bell me-2"></i>
                            My Notifications
                        </h5>
                        @if($unreadCount > 0)
                            <form action="{{ route('customer.notifications.mark-all-read') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-check-double me-1"></i>Mark All Read ({{ $unreadCount }})
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @if($notifications->count() > 0)
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ !$notification->is_read ? 'bg-light border-start border-primary border-3' : '' }}">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 flex-shrink-0">
                                            @if($notification->type === 'order_completed')
                                                <span class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            @elseif($notification->type === 'order_cancelled')
                                                <span class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-times"></i>
                                                </span>
                                            @elseif($notification->type === 'order_placed')
                                                <span class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </span>
                                            @else
                                                <span class="bg-secondary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-bell"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1 fw-bold">{{ $notification->title }}</h6>
                                                    <p class="mb-2 text-muted">{{ $notification->message }}</p>
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
                                                        @if(isset($notification->data['order_id']))
                                                            <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye me-1"></i>View Order
                                                            </a>
                                                        @endif
                                                        @if(!$notification->is_read)
                                                            <form action="{{ route('customer.notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
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
                                <p class="text-muted mb-4">We'll notify you when there are updates to your orders</p>
                                <a href="{{ route('customer.products') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag me-1"></i>Start Shopping
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
</body>
</html>