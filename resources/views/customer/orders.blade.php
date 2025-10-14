<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - My Orders</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    @livewireStyles

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

        /* Navbar */
        .navbar {
            background-color: var(--primary-color) !important;
        }

        .navbar-brand {
            font-weight: bold;
            color: #ffffff !important;
        }

        .navbar-nav .nav-link {
            color: #f1f1f1 !important;
            font-weight: 00;
            text-decoration: none !important; /* Removes underline */
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #ffffff !important;
            text-decoration: none !important; /* Ensures no underline */
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .card-header {
            background-color: var(--primary-color);
            color: #fff;
            border-bottom: none;
        }

        /* Table */
        .table th {
            background-color: #f1f1f1;
            color: #333;
        }

        .table-hover tbody tr:hover {
            background-color: #f9ecec;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.35rem 0.6rem;
            border-radius: 0.4rem;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-complete {
            background-color: #28a745;
            color: #fff;
        }

        .status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .modal-content {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        /* Layout spacing */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .page-header h5 {
            font-weight: 600;
            color: #333;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
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
                <a class="nav-link active" href="{{ route('customer.orders') }}">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>

                @livewire('customer-notification-navbar')

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name ?? 'Customer' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user-edit me-2"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">

        <!-- Back Button -->
        <div class="page-header">
            <button class="btn-back" onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <h5><i class="fas fa-shopping-cart me-2 text-danger"></i>My Orders</h5>
        </div>

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

        <div class="card">
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td><strong>#{{ $order->id }}</strong></td>
                                        <td>
                                            {{ $order->created_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @foreach($order->details->take(2) as $detail)
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-secondary me-2">{{ $detail->quantity }}</span>
                                                    <span>{{ $detail->product->name ?? 'Product' }}</span>
                                                </div>
                                            @endforeach
                                            @if($order->details->count() > 2)
                                                <small class="text-muted">+{{ $order->details->count() - 2 }} more</small>
                                            @endif
                                        </td>
                                        <td><strong>₱{{ number_format($order->details->sum('total'), 2) }}</strong></td>
                                        <td>
                                            @php
                                                $statusClass = match($order->order_status) {
                                                    \App\Enums\OrderStatus::COMPLETE => 'status-complete',
                                                    \App\Enums\OrderStatus::CANCELLED => 'status-cancelled',
                                                    default => 'status-pending'
                                                };
                                            @endphp
                                            <span class="badge status-badge {{ $statusClass }}">
                                                {{ $order->order_status instanceof \App\Enums\OrderStatus ? $order->order_status->label() : ucfirst($order->order_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-eye"></i>View
                                            </a>

                                            @if($order->order_status === \App\Enums\OrderStatus::PENDING)
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal{{ $order->id }}">
                                                    <i class="fas fa-times"></i>Cancel
                                                </button>

                                                <!-- Cancel Modal -->
                                                <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Cancel Order #{{ $order->invoice_no }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="alert alert-warning">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>Are you sure you want to cancel this order? This action cannot be undone.
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Reason for cancellation <span class="text-danger">*</span></label>
                                                                        <textarea class="form-control" name="reason" rows="3" required placeholder="Please provide a reason..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Orders Yet</h5>
                        <p class="text-muted">You haven't placed any orders yet.</p>
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script>
        function goBack() {
            if (document.referrer && document.referrer !== window.location.href) {
                window.history.back();
            } else {
                window.location.href = "{{ route('customer.dashboard') }}";
            }
        }
    </script>
    @livewireScripts
</body>
</html>
